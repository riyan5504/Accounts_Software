// public/backend/dist/js/purchase-calculations.js
$(document).ready(function() {
    
    console.log('=== Purchase Calculator Loading ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery UI Autocomplete:', typeof $.fn.autocomplete);
    console.log('Container exists:', $('#itemContainer').length > 0);
    
    // Check if container exists
    if (!$('#itemContainer').length) {
        console.log('No purchase form on this page, skipping...');
        return;
    }
    
    // Check if autocomplete is available
    if (typeof $.fn.autocomplete === 'undefined') {
        console.error('ERROR: jQuery UI Autocomplete not loaded!');
        alert('jQuery UI Autocomplete is required but not loaded.');
        return;
    }
    
    console.log('Initializing Purchase Calculator...');
    
    // ============ VARIABLES ============
    const container = $('#itemContainer');
    const searchUrl = $('#itemSearchUrl').val() || '/search/item';
    const vendorSearchUrl = $('#vendorSearchUrl').val() || '/search/vendor';
    const accountStatusUrl = $('#accountStatusUrl').val() || '/search/accounts-by-status';
    let itemSerial = parseInt($('#itemSerial').val()) || 1;
    
    console.log('Search URL:', searchUrl);
    console.log('Vendor Search URL:', vendorSearchUrl);
    console.log('Item Serial:', itemSerial);
    
    // ============ ITEM CODE GENERATOR ============
    function generateItemCode(itemName) {
        if (!itemName) return '';
        
        const words = itemName.trim().split(/\s+/);
        let prefix = '';

        if (words.length >= 2) {
            prefix = (words[0][0] + words[1][0]).toUpperCase();
        } else if (words[0].length >= 2) {
            prefix = words[0].substring(0, 2).toUpperCase();
        } else {
            prefix = words[0][0].toUpperCase() + 'X';
        }

        return prefix + String(itemSerial).padStart(2, '0');
    }
    
    // ============ ROW CALCULATIONS ============
    function recalcRow(row) {
        const qty = parseFloat(row.find('.qty').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit_price').val()) || 0;
        const vatPercent = parseFloat(row.find('.vat_percent').val()) || 0;

        const price = qty * unitPrice;
        const vatAmount = price * (vatPercent / 100);
        const totalPrice = price + vatAmount;

        row.find('.price').val(price.toFixed(2));
        row.find('.vat_amount').val(vatAmount.toFixed(2));
        row.find('.total_price').val(totalPrice.toFixed(2));
    }
    
    function calculateTotals() {
        let subTotal = 0;
        let totalVat = 0;

        container.find('.price').each(function() {
            subTotal += parseFloat($(this).val()) || 0;
        });

        container.find('.vat_amount').each(function() {
            totalVat += parseFloat($(this).val()) || 0;
        });

        const disPercent = parseFloat($('.dis_percent').val()) || 0;
        const disAmt = subTotal * (disPercent / 100);
        const paidAmt = parseFloat($('.paid_amt').val()) || 0;
        const grandTotal = subTotal - disAmt + totalVat;
        const dueAmt = Math.max(0, grandTotal - paidAmt);

        $('.sub_total').val(subTotal.toFixed(2));
        $('.vat_amt').val(totalVat.toFixed(2));
        $('.dis_amt').val(disAmt.toFixed(2));
        $('.grand_total').val(grandTotal.toFixed(2));
        $('.due_amt').val(dueAmt.toFixed(2));
    }
    
    // ============ ITEM AUTOCOMPLETE ============
    function setupItemAutocomplete(row) {
        const itemNameInput = row.find('.item_name');
        
        console.log('Setting up autocomplete for:', itemNameInput.attr('name'));
        
        // Destroy existing autocomplete
        if (itemNameInput.hasClass('ui-autocomplete-input')) {
            console.log('Destroying existing autocomplete');
            itemNameInput.autocomplete('destroy');
        }
        
        itemNameInput.autocomplete({
            source: function(request, response) {
                console.log('Searching:', request.term);
                
                $.ajax({
                    url: searchUrl,
                    dataType: 'json',
                    data: { term: request.term },
                    success: function(data) {
                        console.log('Results:', data.length, 'items found');
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Search failed:', status, error);
                        response([]);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                console.log('✅ Item selected:', ui.item);
                
                if (!ui.item) return false;
                itemNameInput.val(ui.item.value || ui.item.label);
                // Fill all fields
                row.find('.item_id').val(ui.item.item_id || ui.item.id || '');
                row.find('.item_code').val(ui.item.item_code || '');
                row.find('.cat_name').val(ui.item.cat_name || '');
                row.find('.size').val(ui.item.size || '');
                row.find('.unit_price').val(ui.item.unit_price || '');
                
                row.data('selected', true);
                
                recalcRow(row);
                calculateTotals();
                
                return false;
            },
            response: function(event, ui) {
                if (ui.content.length === 0) {
                    const val = $(this).val().trim();
                    if (val) {
                        const code = generateItemCode(val);
                        row.find('.item_code').val(code);
                        console.log('Generated code for new item:', code);
                    }
                }
            },
            open: function() {
                row.data('selected', false);
            }
        });
        
        // Manual input
        itemNameInput.off('input').on('input', function() {
            if (!row.data('selected')) {
                row.find('.item_id').val('');
                row.find('.cat_name').val('');
                row.find('.size').val('');
                row.find('.unit_price').val('');
            }
            
            const val = $(this).val().trim();
            row.find('.item_code').val(val ? generateItemCode(val) : '');
        });
    }
    
    // ============ SETUP ALL ROWS ============
    const rows = container.find('.item-row');
    console.log('Found', rows.length, 'item rows');
    
    rows.each(function(index) {
        console.log('Setting up row', index + 1);
        setupItemAutocomplete($(this));
        
        $(this).find('.qty, .unit_price, .vat_percent').on('input', function() {
            recalcRow($(this).closest('.item-row'));
            calculateTotals();
        });
    });
    
    // ============ ADD ITEM BUTTON ============
    $('#addItem').on('click', function() {
    const firstRow = container.find('.item-row').first();
    const newRow = firstRow.clone();

    // reset values
    newRow.find('input').val('');
    newRow.find('select').prop('selectedIndex', 0);
    newRow.find('[id]').remove();
    newRow.data('selected', false);

    // 🔥 FIX START
    const itemInput = newRow.find('.item_name');

    itemInput.removeClass('ui-autocomplete-input');
    itemInput.removeAttr('autocomplete');

    if (itemInput.hasClass('ui-autocomplete-input')) {
        itemInput.autocomplete('destroy');
    }
    // 🔥 FIX END

    container.append(newRow);
    itemSerial++;

    setupItemAutocomplete(newRow);

    newRow.find('.qty, .unit_price, .vat_percent').on('input', function() {
        recalcRow(newRow);
        calculateTotals();
    });

    newRow.find('.item_name').focus();
});
    
    // ============ REMOVE ITEM ============
    container.on('click', '.removeItem', function() {
        const rows = container.find('.item-row');
        if (rows.length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
        }
    });
    
    // ============ DISCOUNT & PAID ============
    $('.dis_percent, .paid_amt').on('input', calculateTotals);
    
    // ============ VENDOR AUTOCOMPLETE ============
    if ($('.v_name').length) {
        
        // Destroy existing autocomplete if any
        if ($('.v_name').hasClass('ui-autocomplete-input')) {
            $('.v_name').autocomplete('destroy');
        }
        
        $('.v_name').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '/search/vendor',
                    dataType: 'json',
                    data: { term: request.term },
                    success: function(data) {
                        console.log('Vendors found:', data.length);
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Vendor search error:', error);
                        response([]);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                console.log('Selected:', ui.item);
                
                // ✅ CRITICAL: Set the input value using $(this)
                $(this).val(ui.item.value);
                
                // Set vendor ID
                $('.vendor_id').val(ui.item.vendor_id || ui.item.id);
                
                // Fill other vendor fields
                $('.phone').val(ui.item.phone || '');
                $('.email').val(ui.item.email || '');
                $('.address').val(ui.item.address || '');
                
                return false;
            }
        });
        
        // Reset vendor_id when user types manually
        $('.v_name').on('input', function() {
            $('.vendor_id').val('');
        });
    }
    
    // ============ PAYMENT STATUS ============
    if ($('.payment_status').length) {
        function loadAccountsByStatus(status) {
            const accountSelect = $('.credit_account_id');
            if (!accountSelect.length) return;
            
            accountSelect.prop('disabled', true).html('<option>Loading...</option>');
            
            $.ajax({
                url: accountStatusUrl + '/' + status,
                type: 'GET',
                success: function(data) {
                    accountSelect.prop('disabled', false).empty()
                        .append('<option selected disabled>Select Credit Account</option>');
                    
                    data.forEach(function(acc) {
                        accountSelect.append(
                            `<option value="${acc.id}">${acc.account_name}</option>`
                        );
                    });
                },
                error: function() {
                    accountSelect.prop('disabled', false)
                        .html('<option>Error loading</option>');
                }
            });
        }
        
        const initialStatus = $('.payment_status').val();
        if (initialStatus) {
            loadAccountsByStatus(initialStatus);
        }
        
        $('.payment_status').on('change', function() {
            loadAccountsByStatus($(this).val());
        });
    }
    
    // ============ DEBIT ACCOUNT FILTER ============
    $('.debit_account_id option').each(function() {
        const acType = $(this).data('ac_type');
        if (acType && acType !== 'asset' && $(this).val()) {
            $(this).hide();
        }
    });
    
    // ============ INITIAL CALCULATION ============
    calculateTotals();
    
    console.log('✅ Purchase Calculator initialized successfully!');
});