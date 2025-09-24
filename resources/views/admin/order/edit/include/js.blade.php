<script>
    const plan = {!! $plan !!};

    function generateRow(year, brand, cardNumber, playerName, notes, quantity) {
        const displayName = `${year} ${brand} ${cardNumber} ${playerName}`;
        return `
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div>${displayName}</div>
                <input type="hidden" name="year[]" value="${year}" class="input-year">
                <input type="hidden" name="brand[]" value="${brand}" class="input-brand">
                <input type="hidden" name="cardNumber[]" value="${cardNumber}" class="input-cardNumber">
                <input type="hidden" name="playerName[]" value="${playerName}" class="input-playerName">
                <input type="hidden" name="notes[]" value="${notes}" class="input-notes">
                <input type="hidden" name="quantity[]" value="${quantity}" class="input-quantity">
            </div>
        </td>
        <td>${quantity}</td>
        <td>
            <div class="d-flex align-items-center">
                <a href="#" class="btn btn-outline-light btn-xs rounded-pill p-2 me-2 edit-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#212121" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                        <path d="M13.5 6.5l4 4" />
                    </svg>
                </a>
                <a href="#" class="btn btn-outline-danger border-light btn-xs rounded-pill p-2 delete-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7l16 0" />
                        <path d="M10 11l0 6" />
                        <path d="M14 11l0 6" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg>
                </a>
            </div>
        </td>
    </tr>
`;
    }

    $(document).ready(function() {
        const $itemEntryWrapper = $('#item-entry-wrapper');

        let editingRow = null;

        const itemUnitPrice = parseInt($('#custom_unit_price').val())


        var custPrice = $('#custom_unit_price').val();
        $('.service-level-checkbox').data('price', custPrice)
        $('.custom-price').text(custPrice)

        $(document).on('click', '.itemAddButton', function(event) {
            event.preventDefault();

            const currentItemCount = $itemEntryWrapper.find('tr').length;

            // if(plan.type=='single' && currentItemCount >= 1){
            //     toastr.warning('In single plan you can not add more than 1 card')
            //     return ;
            // }

            const year = $('#year').val();
            const brand = $('#brand').val();
            const cardNumber = $('#cardNumber').val();
            const playerName = $('#playerName').val();
            const notes = $('#notes').val();
            const quantity = $('#quantity').val();

            if (!year || !brand || !cardNumber || !playerName || !quantity) {
                alert('Please fill out all required fields.');
                return;
            }

            if (editingRow) {

                editingRow.replaceWith(generateRow(year, brand, cardNumber, playerName, notes,
                    quantity));
                    editingRow = null;
            } else {

                $itemEntryWrapper.append(generateRow(year, brand, cardNumber, playerName, notes,
                    quantity));
                    console.log('item added');
                    
            }

            updateQtyPrice()

            $('.entry_form #entry_input_form_wrapper').find('input,select').val('');
        });


        $itemEntryWrapper.on('click', '.delete-item', function(event) {
            event.preventDefault();

            if (confirm("Are you sure?")) {
                $(this).closest('tr').remove();

                const currentItemCount = $itemEntryWrapper.find('tr').length;
                $('.item-count').text(currentItemCount);
                $('.totalprice').text(parseFloat(currentItemCount * itemUnitPrice).toFixed(2));
                updateQtyPrice()
            }
        });

        $itemEntryWrapper.on('click', '.edit-item', function(event) {
            event.preventDefault();
            editingRow = $(this).closest('tr');

            $('#year').val(editingRow.find('.input-year').val());
            $('#brand').val(editingRow.find('.input-brand').val());
            $('#cardNumber').val(editingRow.find('.input-cardNumber').val());
            $('#playerName').val(editingRow.find('.input-playerName').val());
            $('#notes').val(editingRow.find('.input-notes').val());
            $('#quantity').val(editingRow.find('.input-quantity').val());
        });

        $(document).on('click', '.shippingBillingBtn', function(event) {
            event.preventDefault();
            let currentItemsQty = 0;

            $itemEntryWrapper.find('tr').map(function(k, v) {
                currentItemsQty += parseInt($(this).find('[name="quantity[]"]').val())
            });

            if (plan.type == 'general' && currentItemsQty < plan.minimum_card) {
                toastr.error("You have to add minimum " + plan.minimum_card + " card")
                return
            }
            $('#checkoutForm').submit()
        })
        $(document).on('click', '.DiscardBtn', function(event) {
            event.preventDefault();
            $('.entry_form #entry_input_form_wrapper').find('input,select').val('');
            editingRow = null;
        })

        function updateQtyPrice() {
            const currentItemCount = $itemEntryWrapper.find('tr').length;
            $('.item-count').text(currentItemCount);
            let currentItemsQty = 0;
            $itemEntryWrapper.find('tr').map(function(k, v) {
                currentItemsQty += parseInt($(this).find('[name="quantity[]"]').val())
            });

            $('.totalprice').text(parseFloat(currentItemsQty * itemUnitPrice).toFixed(2));
        }

    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.4/typeahead.bundle.min.js"></script>
<script>
    var cardQuery = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '{{ route('api.card.search', '%QUERY%') }}',
            wildcard: '%QUERY%',
            transform: function(response) {
                return response.success ? response.data : [];
            }
        }

    });


    $('#item_search').typeahead(null, {
        name: 'card-search',
        display: function(datum) {
            return `${datum.year || 'N/A'} ${datum.brand_name || 'N/A'} ${datum.card || 'N/A'} ${datum.card_name || 'N/A'}`;
        },
        minLength: 2,
        source: cardQuery,
        templates: {
            empty: [
                '<div class="empty-message">',
                'Unable to find any card, please enter the card info below',
                '</div>'
            ].join('\n'),
        }
    }).on('typeahead:select', function(event, suggestion) {
        $('.entry_form #entry_input_form_wrapper #year').val(suggestion.year);
        $('.entry_form #entry_input_form_wrapper #brand').val(suggestion.brand_name);
        $('.entry_form #entry_input_form_wrapper #cardNumber').val(suggestion.card);
        $('.entry_form #entry_input_form_wrapper #playerName').val(suggestion.card_name);
    });
</script>
<script>
    $('.select2').select2({
        theme: 'bootstrap-5'
    })
    $(document).on('input change', '#custom_unit_price', function(event) {
        var custPrice = $(this).val();
        $('.service-level-checkbox').data('price', custPrice)
        $('.custom-price').text(custPrice)
    });
</script>