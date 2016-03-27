<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit('Direct access is not allowed. This is a console tool, use the terminal');
        }

        $this->load->dbforge(); 
        $this->load->library('migration');       
    }

    public function help() {
        $info = "Available commands through \"php index.php\":\n\n";
        $info .= "tools migration \"file_name\" | Create new migration file\n";
        $info .= "tools migrate \"version_number\" | Run all migrations. The version number is optional.\n";
        $info .= "tools reset | Reset all migrations.\n";

        echo $info . PHP_EOL;
    }

    public function migrate($version = null)
    {
        if ($version) {
            if ($this->migration->version($version)) {
                echo 'Success: migration run.';
            }
            else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo 'Success: migrations run.';
            }
            else {
                show_error($this->migration->error_string());
            }            
        }
    }

    public function migration($name)
    {
        $name = strtolower($name);
        $path = APPPATH . 'migrations/'. date('YmdHis') . '_' . $name .'.php';
        $new_migration = fopen($path, "w");

        if ($new_migration) {
            $data['name'] = $name;
            $migration_template = $this->load->view('tool/migrations', $data, TRUE);

            fwrite($new_migration, $migration_template);
            fclose($new_migration);

            echo 'Success: migration has been created.';
        }
        else {
            echo 'Error: unable to create migration file!';
        }
    }

    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations reset.';            
    }
}