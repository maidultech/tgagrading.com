<?php

namespace App\Services;

use Zendesk\API\HttpClient as ZendeskClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class ZendeskService
{
    protected $client;
    protected $setting;

    public function __construct()
    {
        $this->setting = getSetting();

        $this->client = new ZendeskClient($this->setting->zendesk_subdomain);
        $this->client->setAuth('basic', [
            'username' => $this->setting->zendesk_email,
            'token' => $this->setting->zendesk_token,
        ]);
    }

    /**
     * Get tickets for the user by email.
     */
    public function getTicketsByUserEmail($email)
    {
        try {

            if (!$this->setting || !isset($this->setting->zendesk_subdomain, $this->setting->zendesk_email, $this->setting->zendesk_token)) {
                throw new \Exception("Zendesk settings are not configured properly.");
            }

            $subdomain = $this->setting->zendesk_subdomain;
            $zendeskEmail = $this->setting->zendesk_email;
            $zendeskToken = $this->setting->zendesk_token;

            $auth = base64_encode("{$zendeskEmail}/token:{$zendeskToken}");
            $userResponse = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
            ])->get("https://{$subdomain}.zendesk.com/api/v2/search.json", [
                'query' => "type:user email:{$email}",
            ]);
    
            if (!$userResponse->successful()) {
                throw new \Exception('Failed to search for user: ' . $userResponse->body());
            }
    
            $userResults = $userResponse->json()['results'];
    
            if (empty($userResults)) {
                throw new \Exception("No user found with email: {$email}");
            }
    
            $userId = $userResults[0]['id'];
    
            $ticketsResponse = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
            ])->get("https://{$subdomain}.zendesk.com/api/v2/users/{$userId}/tickets/requested.json");
    
            if (!$ticketsResponse->successful()) {
                throw new \Exception('Failed to fetch tickets: ' . $ticketsResponse->body());
            }
    
            $tickets = $ticketsResponse->json()['tickets'];

            $data['user_id'] = $userId;
            $data['tickets'] = $tickets;

            return $data;
        } catch (\Exception $e) {
            Log::error('Zendesk API error: ' . $e->getMessage());
            throw new \Exception('An error occurred while fetching tickets. Please try again later.');
        }
    }
    
    /**
     * Get User Message details.
     */
    public function getTicketDetailsWithComments($ticketId)
    {

        try {

            if (!$this->setting || !isset($this->setting->zendesk_subdomain, $this->setting->zendesk_email, $this->setting->zendesk_token)) {
                throw new \Exception("Zendesk settings are not configured properly.");
            }

            $subdomain = $this->setting->zendesk_subdomain;
            $zendeskEmail = $this->setting->zendesk_email;
            $zendeskToken = $this->setting->zendesk_token;
    
            $auth = base64_encode("{$zendeskEmail}/token:{$zendeskToken}");
    
            // Fetch ticket details
            $ticketResponse = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
            ])->get("https://{$subdomain}.zendesk.com/api/v2/tickets/{$ticketId}.json");
    
            if (!$ticketResponse->successful()) {
                throw new \Exception('Failed to fetch ticket details: ' . $ticketResponse->body());
            }
    
            $ticketDetails = $ticketResponse->json();
    
            // Fetch ticket comments
            $commentsResponse = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
            ])->get("https://{$subdomain}.zendesk.com/api/v2/tickets/{$ticketId}/comments.json");
    
            if (!$commentsResponse->successful()) {
                throw new \Exception('Failed to fetch ticket comments: ' . $commentsResponse->body());
            }
    
            $comments = collect($commentsResponse->json()['comments'])
                ->where('public', true)
                ->all();
    
            return [
                'ticket' => $ticketDetails['ticket'],
                'comments' => $comments,
            ];

        } catch (\Exception $e) {
            Log::error('Zendesk API error: ' . $e->getMessage());
            throw new \Exception('An error occurred while fetching tickets. Please try again later.');
        }

    }

    /**
     * Upload a file to Zendesk and return the upload token.
     */
    public function uploadAttachment($file)
    {
        try {

            if (!$this->setting || !isset($this->setting->zendesk_subdomain, $this->setting->zendesk_email, $this->setting->zendesk_token)) {
                throw new \Exception("Zendesk settings are not configured properly.");
            }

            $randomName = Str::random(15) . '.' . $file->getClientOriginalExtension();
            $attachment = $this->client->attachments()->upload([
                'file' => $file->getPathname(),
                'type' => $file->getMimeType(),
                'name' => $randomName,
            ]);
    
            return $attachment->upload->token;
        } catch (\Exception $e) {
            throw new \Exception('Failed to upload attachment: ' . $e->getMessage());
        }
    }

    /**
     * Create a ticket with attachments.
     */
    public function createTicketWithAttachments($subject, $message, $requester, $attachments = [], $priority = 'normal', $type = null)
    {
        try {

            if (!$this->setting || !isset($this->setting->zendesk_subdomain, $this->setting->zendesk_email, $this->setting->zendesk_token)) {
                throw new \Exception("Zendesk settings are not configured properly.");
            }

            $uploadTokens = [];
            foreach ($attachments as $file) {
                $uploadTokens[] = $this->uploadAttachment($file);
            }
    
            $data = [
                'subject' => $subject,
                'priority' => $priority,
                'status' => 'pending',
                'comment' => [
                    'body' => $message,
                    'uploads' => $uploadTokens,
                ],
                'requester' => [
                    'name' => $requester['name'],
                    'email' => $requester['email'],
                ],
            ];
            
            if ($type != null) {
                $data['type'] = $type;
            }
            
            return $this->client->tickets()->create($data);

        } catch (\Exception $e) {
            throw new \Exception('Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function replyToTicket($ticketId, $requesterId, $message, $attachments = [])
    {
        try {

            if (!$this->setting || !isset($this->setting->zendesk_subdomain, $this->setting->zendesk_email, $this->setting->zendesk_token)) {
                throw new \Exception("Zendesk settings are not configured properly.");
            }

            $subdomain = $this->setting->zendesk_subdomain;
            $zendeskEmail = $this->setting->zendesk_email;
            $zendeskToken = $this->setting->zendesk_token;
    
            $auth = base64_encode("{$zendeskEmail}/token:{$zendeskToken}");
    
            $uploadTokens = [];
            foreach ($attachments as $file) {
                $uploadTokens[] = $this->uploadAttachment($file);
            }
    
            $replyResponse = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
                'Content-Type' => 'application/json',
            ])->put("https://{$subdomain}.zendesk.com/api/v2/tickets/{$ticketId}.json", [
                'ticket' => [
                    'comment' => [
                        'body' => $message,
                        'public' => true,
                        'uploads' => $uploadTokens,
                        'author_id' => $requesterId,
                    ],
                ],
            ]);
            
    
            if (!$replyResponse->successful()) {
                throw new \Exception('Failed to reply to ticket: ' . $replyResponse->body());
            }
    
            return $replyResponse->json();

        } catch (\Exception $e) {
            throw new \Exception('Failed to reply to ticket: ' . $e->getMessage());
        }
    }
}
