
    <?php
    class Ret_estimation extends CI_Controller {
        function get_data() {
            $this->db->get('users');
            $this->db->query("SELECT * FROM orders JOIN items ON id");
        }
    }
    