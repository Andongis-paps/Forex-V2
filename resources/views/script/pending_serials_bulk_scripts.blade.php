<script>
    $(document).ready(function() {
        $("#pending-serials-form").validate();

        // $('[name^="serials"]').each(function() {
        //     if ($(this).hasClass('serials-input')) {
        //         $(this).rules('add', {
        //             // required: true,
        //             minlength: 6,
        //             maxlength: 12,
        //             pattern: /^[a-zA-Z0-9]+$/,
        //             messages: {
        //                 // required: "Please enter a serial.",
        //                 minlength: "Serial must be at least 6 characters long.",
        //                 maxlength: "Serial can't exceed 12 characters.",
        //                 pattern: "Serial/s must be in a alphanumeric format. (No special characters should be present)",
        //                 duplicate: "Theres a duplicate serial."
        //             },
        //         });
        //     }
        // });

        $.validator.addMethod("duplicate", function(value, element) {
            var input = $('[name^="serials"]');

            var input_fields_val = input.map(function() {
                return $(this).val();
            }).get();

            var duplicate_count = $.grep(input_fields_val, function(field_values) {
                return field_values == value;
            }).length;

            return duplicate_count <= 1;
        }, "Value already exists");

        $("#submit-peding-serials").click(function() {
            if ($("#pending-serials-form").valid() ) {
                return true

                $(this).removeAttr('disabled', 'disabled');
            } else {
                $('#add-pending-serials-modal').modal("hide");
            }
        });
    });
</script>
