
    function savedata() {
        $.ajax({
            url: "index.php/admin_test/save_data",
            success: function() {
                updateUI();
            }
        });
    }
    