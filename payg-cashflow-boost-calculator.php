<?php
   /*
      Plugin Name: PAYGW Cashflow Boost Calculator
      Plugin URI: https://jameshwartlopez.com/
      description: PAYGW Cashflow Boost Calculator for Australian Small Business Employers. This advanced forecast tool is based on the ATO guidance and the current legislation before parliament. It is subject to change and is provided as a guide
      Version: 1.0
      Author: Jameshwart Lopez
      Author URI: https://jameshwartlopez.com/paygw/
      License: GPLv2 or later
      License URI: http://www.gnu.org/licenses/gpl-2.0.html
   */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'PAYG_PLUGIN_FILE' ) ) {
   define( 'PAYG_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PAYG_PLUGIN_DIR' ) ) {
   define( 'PAYG_PLUGIN_DIR', dirname( PAYG_PLUGIN_FILE )  );
}



if ( !class_exists( 'PAYG_Cashflow' ) ) {

   class PAYG_Cashflow {

      protected static $_instance = null;

      public static function instance() {
         if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
         }
         return self::$_instance;
      }


      public function __construct() {

         $this->shortCode();
         $this->enqueScripts();
         $this->ajaxHooks();

         add_action('admin_menu', array($this, 'pluginMenu'));
      }

      public function pluginMenu() {
          add_menu_page( 'PAYGW Cashflow Calculator', 'PAYGW Cashflow', 'manage_options', 'payg_calculator', array($this, 'adminPage') );
      }

      public function adminPage() {
         echo do_shortcode('[payg_calculator]');
         echo '<strong>Note:</strong> If you want to show this calculator in the frontend then use the shortcode <span><pre>[payg_calculator]</pre></span>';
      }



      public function shortCode() {

         add_shortcode( 'payg_calculator', array($this, 'formOutput'));

      }

      public function formOutput() {

         ob_start();
         include_once PAYG_PLUGIN_DIR . '/view/form.php';
         return ob_get_clean();

      }

      public function enqueScripts(){

         add_action( 'admin_enqueue_scripts', array($this, 'stylesAndScripts') );
         add_action( 'wp_enqueue_scripts', array($this, 'stylesAndScripts') );

      }

      
      public function stylesAndScripts() {
         $plugin_dir_url = plugin_dir_url(PAYG_PLUGIN_FILE);
         $src =  $plugin_dir_url . 'assets/payg-v3.js';
         
         wp_enqueue_style( 'payg', $plugin_dir_url . 'assets/payg.css');
         wp_enqueue_script( 'payg-js', $src, array ( 'jquery' ), 1.1, true );

         // in JavaScript, object properties are accessed as pay_object.ajax_url, pay_object.we_value
         wp_localize_script( 'payg-js', 'pay_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
      }


      public function ajaxHooks() {
            add_action( 'wp_ajax_calculate_pay', array($this, 'calculatePayg' ) );
            add_action( 'wp_ajax_nopriv_calculate_pay', array($this, 'calculatePayg' ) );
      }

      public function moneyFormat($money) {
         return '$'.number_format($money, 0, '.', ',');
      }

      public function calculatePayg() {
         
         $payg_report = sanitize_key($_POST['values']['paygReporting']);

         
         if('quarterly' == $payg_report){
            
            $q3 = sanitize_key($_POST['values']['quarterly_amount3']);
            
            $q3_concession  = 0;

            if(doubleval($q3) < 10000){
               $q3_concession = 10000;
            } else if(doubleval($q3) >= 10000) {
              
               if(doubleval($q3) >= 50000){
                  $q3_concession = 50000;
               } else {
                  $q3_concession = $q3;
               }
            }


            $q4 = sanitize_key($_POST['values']['quarterly_amount4']);
            $q4_concession = 0;
            $q4_concession_tmp1 = $q3 + $q4;
            

            if($q4_concession_tmp1 <= 10000){
               $q4_concession = 0;
            } else {

               $q4_concession_tmp2 = $q4 + $q3_concession;
               if($q4_concession_tmp2 < 50000){
                  $q4_concession = doubleval($q4) + doubleval($q3) - doubleval($q3_concession);
               } else {
                  $q4_concession = 50000 - $q3_concession;
               }
            } 

            
            $q4_next_year = (doubleval($q4_concession) + doubleval($q3_concession)) / 2;
            $q4_next_year_concession = 0;
            if($q4_next_year < 50000){
               $q4_next_year_concession = $q4_next_year;
            } else {
               $q4_next_year_concession = 50000;
            }

            $q1_next_year = (doubleval($q4_concession) + doubleval($q3_concession)) / 2;
            $q1_next_year_concession = 0;
            if((doubleval($q1_next_year) + doubleval($q4_next_year_concession)) < 50000){
               $q1_next_year_concession = $q1_next_year;
            } else {
               $q1_next_year_concession = 50000 - doubleval($q4_next_year_concession);
            }

            $grand_total = doubleval($q3_concession) + doubleval($q4_concession) + doubleval($q4_next_year) + doubleval($q1_next_year);

            echo '<br/><h3>TOTAL CASH FLOW ASSISTANCE: <strong>'.$this->moneyFormat($grand_total).'</strong></h3>';

            ?>
               <table>
                  <thead>
                     <tr>
                        <th>Period</th>
                        <th>Cash Payment Date</th>
                        <th>Cash flow concession ($)</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Jan - Mar (Q3)</td>
                        <td>28 April</td>
                        <td>
                        <?php 

                           echo $this->moneyFormat($q3_concession);
                        ?>
                        </td>
                     </tr>
                     <tr>
                        <td>Apr - Jun (Q4)</td>
                        <td>28 July</td>
                        <td>
                           <?php 
                            $q4_total_concession = doubleval($q4_concession) + doubleval($q4_next_year_concession); 
                           echo $this->moneyFormat($q4_total_concession);
                           ?>
                              
                           </td>
                     </tr>
                     <tr>
                        <td>July - Sept (Q1)</td>
                        <td>28 October</td>
                        <td><?php echo $this->moneyFormat($q1_next_year_concession);?></td>
                     </tr>
                  </tbody>

               </table>
            <?php
         } else if('monthly' == $payg_report) {
            
            $month1 = doubleval(sanitize_key($_POST['values']['month1'])) * 3;
            $month2 = sanitize_key($_POST['values']['month2']);
            $month3 = sanitize_key($_POST['values']['month3']);
            $month4 = sanitize_key($_POST['values']['month4']);

            $month5 = $month6 = $month7 = $month8 = 0;

            $month1_concession = 0;
            $month2_concession = 0;
            $month3_concession = 0;
            $month4_concession = 0;
            $month5_concession = 0;
            $month6_concession = 0;
            $month7_concession = 0;
            $month8_concession = 0;


            // month 1 concession calculation
            if($month1 < 10000){
               $month1_concession = 10000;
            } else {
               if($month1 < 50000){
                  $month1_concession = $month1;
               } else {
                  $month1_concession = 50000;
               }
            }

            // month 2 concession calculation
            $month1and2_total = $month1 + $month2;
            if($month1and2_total <= 10000){
               $month2_concession = 0;
            } else {

               if($month1and2_total < 50000) {
                  $month2_concession = doubleval($month1and2_total) - doubleval($month1_concession);
               } else {
                  $month2_concession = 50000 - doubleval($month1_concession);
               }
            }


            // month 3 concession calculation
            $month1to3_total = $month1and2_total + $month3;
            if($month1to3_total <= 10000){
               $month3_concession = 0;
            } else {
               if($month1to3_total < 50000){
                  $month3_concession = doubleval($month1to3_total) - doubleval($month1_concession) - doubleval($month2_concession); 
               } else {
                  $month3_concession = 50000 - doubleval($month1_concession) - doubleval($month2_concession);
               }
            }

            // month 4 concession calculation
            $month1to4_total = $month1to3_total + $month4;
            if($month1to4_total <= 10000){
               $month4_concession = 0;
            } else {
               if($month1to4_total < 50000){
                  $month4_concession = doubleval($month1to4_total) - doubleval($month1_concession) - doubleval($month2_concession) - doubleval($month3_concession); 
               } else {
                  $month4_concession = 50000 - doubleval($month1_concession) - doubleval($month2_concession) - doubleval($month3_concession); 
               }
            }

            $month1to4_total_concession = doubleval($month1_concession) + doubleval($month2_concession) + doubleval($month3_concession) + doubleval($month4_concession);
            
            $month5 = $month6 = $month7 = $month8 = $month1to4_total_concession / 4;
            
            // month 5 concession calculation
            if($month5 < 50000){
               $month5_concession = $month5;
            } else {
               $month5_concession = 50000;
            }

            // month 6 concession calculation
            if((doubleval($month6) + doubleval($month5_concession)) < 50000) {
               $month6_concession = $month6;
            } else {
               $month6_concession = 50000 - doubleval($month5_concession);
            }

            // month 7 concession calculation
            $totalformonth7_concession = doubleval($month7) + doubleval($month5_concession) + doubleval($month6_concession);
            if($totalformonth7_concession < 50000){
               $month7_concession = $month7;
            } else {
               $month7_concession = 50000 - doubleval($month5_concession) - doubleval($month6_concession);
            }


            //month 8 concession calculation
            $totalformonth8_concession = doubleval($month8) + doubleval($month5_concession) + doubleval($month6_concession) + doubleval($month7_concession);
            if($totalformonth8_concession < 50000){
               $month8_concession = $month8;
            } else {
               $month8_concession = 50000 - doubleval($month5_concession) - doubleval($month6_concession) - doubleval($month7_concession);
            }

            $grand_total = doubleval($month1_concession) + doubleval($month2_concession) + doubleval($month3_concession) + doubleval($month4_concession) + doubleval($month5_concession) + doubleval($month6_concession) + doubleval($month7_concession) + doubleval($month8_concession);
            
            echo '<br/><h3>Total Cash Flow Assistance: <strong>'.$this->moneyFormat($grand_total).'</strong></h3>';
            ?>
            <table>
                  <thead>
                     <tr>
                        <th>Period</th>
                        <th>Cash Payment Date</th>
                        <th>Cash flow concession ($)</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>March IAS</td>
                        <td>28 April</td>
                        <td>
                        <?php 
                           echo $this->moneyFormat($month1_concession);
                        ?>
                        </td>
                     </tr>
                     <tr>
                        <td>April IAS</td>
                        <td>21 April</td>
                        <td>
                           <?php  
                           echo $this->moneyFormat($month2_concession);
                           ?>
                              
                           </td>
                     </tr>
                     <tr>
                        <td>May IAS</td>
                        <td>21 May</td>
                        <td><?php echo $this->moneyFormat($month3_concession);?></td>
                     </tr>
                     <tr>
                        <td>June IAS</td>
                        <td>21 July</td>
                        <td>
                        <?php
                           $total_june_concession = doubleval($month4_concession) + doubleval($month5_concession); 
                           echo $this->moneyFormat($total_june_concession);
                        ?>
                        </td>
                     </tr>
                     <tr>
                        <td>July IAS</td>
                        <td>21 August</td>
                        <td>
                           <?php 
                           echo $this->moneyFormat($month6_concession);
                           ?>
                              
                           </td>
                     </tr>
                     <tr>
                        <td>August IAS</td>
                        <td>21 September</td>
                        <td><?php echo $this->moneyFormat($month7_concession);?></td>
                     </tr>
                     <tr>
                        <td>September IAS</td>
                        <td>21 October</td>
                        <td><?php echo $this->moneyFormat($month8_concession);?></td>
                     </tr>
                  </tbody>

               </table>
            <?php
         }
            
            exit();
      }

   }   

}


function PAYG() {
   return PAYG_Cashflow::instance();
}



$GLOBALS['payg'] = PAYG();