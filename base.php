<?php
class Addon_Base_Setup
{

    function __construct()
    {
        add_action('elementor/init', array($this, 'initiate_elementor_addons'));

        add_action('elementor/widgets/widgets_registered', array($this, 'addons_widget_register'));

        add_action('elementor/frontend/after_register_scripts', array($this, 'enqueue_script'));

        add_action('elementor/frontend/after_register_styles', array($this, 'register_frontend_styles'), 10);

        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);
    }

    public function addons_widget_register()
    {
        require_once (EC_EXTENTIONS_PATH . 'widgets/hello.php');
    }

    //Create new section on elementor
    public function initiate_elementor_addons()
    {
        Elementor\Plugin::instance()->elements_manager->add_category(
            'my-section',
            array(
                'title' => __('Addon Elementor Extentions', 'addon_elementor')
            ),
            1
        );
    }

    public function enqueue_script(){
        wp_register_script( 'general-script', EC_EXTENTIONS_URL . 'assets/js/script.js', [ 'jquery' ], EC_ELEMENTOR_VERSION, true );
    }

    public function register_frontend_styles(){
        wp_register_style('general-style', EC_EXTENTIONS_URL . 'assets/css/general.css', array(), EC_ELEMENTOR_VERSION);
    }

    public function enqueue_frontend_styles(){
        wp_enqueue_style('general-style');
    }
}

new Addon_Base_Setup();