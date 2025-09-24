@extends('admin.layouts.master')
@section('manual_label', 'active')
@section('title') {{ $title ?? 'Edit manual Label' }} @endsection
@push('style')
    {{-- <style>
    .hr-text {
        display: flex;
        align-items: center;
        margin: 2rem 0;
        font-size: .825rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        line-height: 1rem;
        color: #6c7a91;
        height: 1px;
    }
    .hr-text:before {
        content: "";
        margin-right: .5rem;
    }
    .hr-text:after, .hr-text:before {
        flex: 1 1 auto;
        height: 1px;
        background-color: #dce1e7;
    }
    .hr-text:after {
        content: "";
        margin-left: .5rem;
    }
</style> --}}
<style>
    .form-check {
      width: 20px;
      height: 20px;
    }
</style>
@endpush


@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">{{ $title }}</h3>
                                    </div>

                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.manual-label.index') }}"
                                                class="btn btn-primary btn-gradient btn-sm">{{ __('messages.common.back') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <form action="{{ route('admin.manual-label.update', $row->id) }}" method="post">
                                    @csrf
                                    <div class="">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="year" class="form-label">Year <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="text" name="years" id="year" class="form-control"
                                                        value="{{ old('years', $row->year) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="brand_name" class="form-label">Brand<span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="text" name="brand_name" id="brand_name" class="form-control"
                                                        value="{{ old('brand_name', $row->brand_name) }}" required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="card" class="form-label">Card #</label>
                                                    <input type="text" name="card" id="card" class="form-control"
                                                        value="{{ old('card', $row->card) }}">
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="card_name" class="form-label">Player/Card Name<span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="text" name="card_name" id="card_name"
                                                        class="form-control"
                                                        value="{{ old('card_name', $row->card_name) }}"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="notes" class="form-label">Notes </label>
                                                    <input type="text" name="notes" id="notes"
                                                        class="form-control" placeholder="notes"
                                                        value="{{ old('notes', $row->notes) }}" >
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="card_number" class="form-label">Certificate Number <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" name="card_number" id="card_number"
                                                        class="form-control" placeholder="Enter card number"
                                                        value="{{ old('card_number', $row->card_number) }}" required>
                                                </div>
                                            </div>
                                            
                                         
                                            {{-- <div class="col-6">
                                                <div class="form-group">
                                                    <label for="grade" class="form-label">Grade <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" step="0.5" name="grade" id="grade"
                                                        class="form-control"
                                                        value="{{ old('grade', $row->grade) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="grade_name" class="form-label">Grade Name<span class="text-success font-bold">*</span></label>
                                                    <input type="text" name="grade_name" id="grade_name"
                                                        class="form-control" placeholder="Enter grade name"
                                                        value="{{ old('grade_name', $row->grade_name) }}" required>
                                                </div>
                                            </div> --}}
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="surface" class="form-label">Surface <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" step="0.5" max="10" min="0" name="surface" id="surface"
                                                        class="form-control"
                                                        value="{{ old('surface', $row->surface) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="centering" class="form-label">Centering <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" step="0.5" max="10" min="0" name="centering" id="centering"
                                                        class="form-control"
                                                        value="{{ old('centering', $row->centering) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="corners" class="form-label">Corners <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" step="0.5" max="10" min="0" name="corners" id="corners"
                                                        class="form-control"
                                                        value="{{ old('corners', $row->corners) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="edges" class="form-label">Edges <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="number" step="0.5" max="10" min="0" name="edges" id="edges"
                                                        class="form-control"
                                                        value="{{ old('edges', $row->edges) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="qr_link" class="form-label">QR Link <span
                                                            class="text-success font-bold">*</span></label>
                                                    <input type="text" name="qr_link" id="qr_link"
                                                        class="form-control"
                                                        value="{{ old('qr_link', $row->qr_link) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6 d-flex align-items-center">
                                                <div class="custom-checkbox-container d-flex align-items-center mt-3">
                                                    <input type="checkbox" id="is_authentic_check" name="is_authentic_check"
                                                       {{$row->is_authentic == 1 ? 'checked' : ''}} class="form-check" value="1">
                                                    <label for="is_authentic_check" class="ml-2 mb-0" style="font-size: 16px">Authentic</label>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#card_number').on('input', function() {
                var cardNumber = $(this).val(); // Get the value of card_number input
                var qrLink = $('#qr_link'); // Get the qr_link input
                
                // Get the current qr_link value
                var currentLink = "{{ $row->qr_link }}";
                console.log(currentLink);
                
                // Replace '___' with the card number in the qr_link
                var updatedLink = currentLink.replace(/(number=)[^&]*/, '$1' + cardNumber);
                
                // Set the updated link back to the qr_link input
                qrLink.val(updatedLink);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('is_authentic_check');
            const inputs = ['surface', 'centering', 'corners', 'edges'].map(id => document.getElementById(id));

            function toggleInputs(shouldDisable) {
                inputs.forEach(input => {
                    input.disabled = shouldDisable;

                    if (shouldDisable) {
                        input.value = '';
                        input.removeAttribute('required');
                    } else {
                        input.setAttribute('required', 'required');
                    }
                });
            }

            toggleInputs(checkbox.checked);
            checkbox.addEventListener('change', function () {
                toggleInputs(this.checked);
            });
        });
    </script>
@endpush