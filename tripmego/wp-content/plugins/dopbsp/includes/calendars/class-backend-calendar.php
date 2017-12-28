<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin (PRO)
* Version                 : 2.1.3
* File                    : includes/calendars/class-backend-calendar.php
* File Version            : 1.1.1
* Created / Last Modified : 14 December 2015
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : Back end calendar PHP class.
*/

    if (!class_exists('DOPBSPBackEndCalendar')){
        class DOPBSPBackEndCalendar extends DOPBSPBackEndCalendars{
            /*
             * Constructor
             */
            function __construct(){
            }

            /* 
             * Returns a JSON with calendar's data & options.
             * 
             * @post id (integer): calendar ID
             * 
             * @return options JSON
             */
            function getOptions(){
                global $wpdb;
                global $DOPBSP;
                
                $id = $_POST['id'];
                
                $settings_calendar = $DOPBSP->classes->backend_settings->values($id,  
                                                                                'calendar');
                
                $calendar = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->calendars.' WHERE id=%d',
                                                               $id));

                $data = array('AddLastHourToTotalPrice' => $settings_calendar->hours_add_last_hour_to_total_price,
                              'AddtMonthViewText' => $DOPBSP->text('CALENDARS_CALENDAR_ADD_MONTH_VIEW'),
                              'AvailableDays' => explode(',', $settings_calendar->days_available),
                              'AvailableLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_AVAILABLE_LABEL'),
                              'AvailableOneText' => $DOPBSP->text('CALENDARS_CALENDAR_AVAILABLE_ONE_TEXT'),
                              'AvailableText' => $DOPBSP->text('CALENDARS_CALENDAR_AVAILABLE_TEXT'),
                              'BookedText' => $DOPBSP->text('CALENDARS_CALENDAR_BOOKED_TEXT'),
                              'Currency' => $DOPBSP->classes->currencies->get($settings_calendar->currency),
                              'DateEndLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_DATE_END_LABEL'),
                              'DateStartLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_DATE_START_LABEL'),
                              'DateType' => 1,
                              'DayNames' => array($DOPBSP->text('DAY_SUNDAY'), 
                                                  $DOPBSP->text('DAY_MONDAY'), 
                                                  $DOPBSP->text('DAY_TUESDAY'), 
                                                  $DOPBSP->text('DAY_WEDNESDAY'), 
                                                  $DOPBSP->text('DAY_THURSDAY'), 
                                                  $DOPBSP->text('DAY_FRIDAY'), 
                                                  $DOPBSP->text('DAY_SATURDAY')),
                              'DetailsFromHours' => $settings_calendar->days_details_from_hours,
                              'FirstDay' => $settings_calendar->days_first,
                              'HoursEnabled' => $settings_calendar->hours_enabled,
                              'GroupDaysLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_GROUP_DAYS_LABEL'),
                              'GroupHoursLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_GROUP_HOURS_LABEL'),
                              'HourEndLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_END_LABEL'),
                              'HourStartLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_START_LABEL'),
                              'HoursAMPM' => $settings_calendar->hours_ampm,
                              'HoursDefinitions' => json_decode($settings_calendar->hours_definitions),
                              'HoursDefinitionsChangeLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_DEFINITIONS_CHANGE_LABEL'),
                              'HoursDefinitionsLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_DEFINITIONS_LABEL'),
                              'HoursSetDefaultDataLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_SET_DEFAULT_DATA_LABEL'),
                              'HoursIntervalEnabled' => $settings_calendar->hours_interval_enabled,
                              'HoursIntervalAutobreakEnabled' => $settings_calendar->hours_interval_autobreak_enabled,
                              'ID' => $id,
                              'DefaultSchedule' => $calendar->default_availability != '' ? $calendar->default_availability:'{"available":1,"bind":0,"hours":{},"hours_definitions":[{"value":"00:00"}],"info":"","notes":"","price":0,"promo":0,"status":"none"}',
                              'InfoLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_INFO_LABEL'),
                              'MaxYear' => $DOPBSP->classes->backend_calendar->getMaxYear($id),
                              'MonthNames' => array($DOPBSP->text('MONTH_JANUARY'), 
                                                    $DOPBSP->text('MONTH_FEBRUARY'), 
                                                    $DOPBSP->text('MONTH_MARCH'),
                                                    $DOPBSP->text('MONTH_APRIL'), 
                                                    $DOPBSP->text('MONTH_MAY'), 
                                                    $DOPBSP->text('MONTH_JUNE'), 
                                                    $DOPBSP->text('MONTH_JULY'), 
                                                    $DOPBSP->text('MONTH_AUGUST'), 
                                                    $DOPBSP->text('MONTH_SEPTEMBER'), 
                                                    $DOPBSP->text('MONTH_OCTOBER'), 
                                                    $DOPBSP->text('MONTH_NOVEMBER'), 
                                                    $DOPBSP->text('MONTH_DECEMBER')),
                              'NextMonthText' => $DOPBSP->text('CALENDARS_CALENDAR_NEXT_MONTH'),
                              'NotesLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_HOURS_NOTES_LABEL'),
                              'PreviousMonthText' => $DOPBSP->text('CALENDARS_CALENDAR_PREVIOUS_MONTH'),
                              'PriceLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_PRICE_LABEL'),
                              'PromoLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_PROMO_LABEL'),
                              'RemoveMonthViewText' => $DOPBSP->text('CALENDARS_CALENDAR_REMOVE_MONTH_VIEW'),
                              'ResetConfirmation' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_RESET_CONFIRMATION'),
                              'SetDaysAvailabilityLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_SET_DAYS_AVAILABILITY_LABEL'),
                              'SetHoursAvailabilityLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_SET_HOURS_AVAILABILITY_LABEL'),
                              'SetHoursDefinitionsLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_SET_HOURS_DEFINITIONS_LABEL'),
                              'StatusAvailableText' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_STATUS_AVAILABLE_TEXT'),
                              'StatusBookedText' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_STATUS_BOOKED_TEXT'),
                              'StatusLabel' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_STATUS_LABEL'),
                              'StatusSpecialText' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_STATUS_SPECIAL_TEXT'),
                              'StatusUnavailableText' => $DOPBSP->text('CALENDARS_CALENDAR_FORM_STATUS_UNAVAILABLE_TEXT'),
                              'UnavailableText' => $DOPBSP->text('CALENDARS_CALENDAR_UNAVAILABLE_TEXT'));

                echo json_encode($data);

                die();
            }
        
            /*
             * Add calendar.
             * 
             * @param $post_id (integer): post ID
             * @param $name (string): calednar name
             */
            function add($post_id = 0,
                         $name = ''){
                global $wpdb;
                global $DOPBSP;
                
                $name = $name == '' ?  $DOPBSP->text('CALENDARS_ADD_CALENDAR_NAME'):$name;
                
                /*
                 * Add calendar.
                 */
                $wpdb->insert($DOPBSP->tables->calendars, array('user_id' => wp_get_current_user()->ID,
                                                                'name' => $name,
                                                                'post_id' => $post_id));
                
                /*
                 * Display new calendars list.
                 */
                if ($post_id == 0){
                    $this->display();
                    die();
                }
            }
            
            /*
             * Duplicate calendar.
             * 
             * @param $calendar_id (integer): calendar ID
             */
            function duplicate(){
                global $wpdb;
                global $DOPBSP;
                
                $calendar_id = $_POST['id'];
                
                /*
                 * Get calendar settings.
                 */
                
                $calendar   = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->calendars.' WHERE id=%d',
                                                            $calendar_id));
                
                /*
                 * Add calendar.
                 */
                
                $wpdb->insert($DOPBSP->tables->calendars, array('user_id' => wp_get_current_user()->ID,
                                                                'name' => $calendar->name,
                                                                'post_id' => $calendar->post_id,
                                                                'max_year' => $calendar->max_year,
                                                                'hours_enabled' => $calendar->hours_enabled,
                                                                'hours_interval_enabled' => $calendar->hours_interval_enabled,
                                                                'price_min' => $calendar->price_min,
                                                                'price_max' => $calendar->price_max,
                                                                'rating' => $calendar->rating,
                                                                'address' => $calendar->address,
                                                                'address_en' => $calendar->address_en,
                                                                'address_alt' => $calendar->address_alt,
                                                                'address_alt_en' => $calendar->address_alt_en,
                                                                'coordinates' => $calendar->coordinates));
                $calendar_new_id = $wpdb->insert_id;
                
                /*
                 * Get calendar settings.
                 */
                $settings_calendars      = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->settings_calendar.' WHERE calendar_id=%d',
                                                              $calendar_id));
                /*
                 * Get notifications settings.
                 */
                $settings_notifications  = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->settings_notifications.' WHERE calendar_id=%d',
                                                              $calendar_id));
                /*
                 * Get payment settings.
                 */
                $settings_payments       = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->settings_payment.' WHERE calendar_id=%d',
                                                         $calendar_id));
                
                
                
                /*
                 * Add calendar settings.
                 */
                foreach($settings_calendars as $settings_calendar) {
                        $wpdb->insert($DOPBSP->tables->settings_calendar, array('calendar_id' => $calendar_new_id,
                                                                                'unique_key' => $settings_calendar->unique_key,
                                                                                'value' => $settings_calendar->value));
                }
                
                /*
                 * Add calendar notifications settings.
                 */
                foreach($settings_notifications as $settings_notification) {
                        $wpdb->insert($DOPBSP->tables->settings_notifications, array('calendar_id' => $calendar_new_id,
                                                                                'unique_key' => $settings_notification->unique_key,
                                                                                'value' => $settings_notification->value));
                }
                
                /*
                 * Add calendar payment settings.
                 */
                foreach($settings_payments as $settings_payment) {
                        $wpdb->insert($DOPBSP->tables->settings_payment, array('calendar_id' => $calendar_new_id,
                                                                                'unique_key' => $settings_payment->unique_key,
                                                                                'value' => $settings_payment->value));
                }
                
                /*
                 * Add calendar availability.
                 */
                $schedule = array();
                
                $days = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->days.' WHERE calendar_id=%d',
                                                          $calendar_id));

                foreach ($days as $day):
                    $schedule[$day->day] = $day->data;
                endforeach;
                
                $schedule = json_encode($schedule);
                $schedule = json_decode($schedule);

                if (count($schedule) > 0){
                    
                    $settings_calendar = $DOPBSP->classes->backend_settings->values($calendar_id,  
                                                                                    'calendar');
                    $hours_enabled = $settings_calendar->hours_enabled;

                    $days = array();
                    $query_insert_values = array();

                    /*
                     * Set days data.
                     */
                    while ($data = current($schedule)){
                        $price_min  = 1000000000;
                        $price_max  = 0;

                        $day = key($schedule);
                        array_push($days, $day);
                        $day_items = explode('-', $day);

                        $control_data = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->days.' WHERE calendar_id=%d AND day="%s"',
                                                                          $calendar_new_id, $day));
                        $data = str_replace('"\"', '',$data);
                        $data = str_replace('\"', '"',$data);
                        $data = str_replace('\\', '',$data);
                        $data = str_replace('}""', '}',$data);
                        $data = stripslashes($data);
                        $data = json_decode($data);
                        
                        if ($hours_enabled == 'true'){
                            foreach ($data->hours as $hour):
                                $price = $hour->promo == '' ? ($hour->price == '' ? 0:(float)$hour->price):(float)$hour->promo;

                                if ($hour->price != '0'){
                                    $price_min = $price < $price_min ? $price:$price_min;
                                    $price_max = $price > $price_max ? $price:$price_max;
                                }
                            endforeach;
                        }
                        else{
                            $price_min = $data->promo == '' ? ($data->price == '' ? 0:(float)$data->price):(float)$data->promo;
                            $price_max = $price_min;
                        }

                        if ($wpdb->num_rows != 0){
                            $wpdb->update($DOPBSP->tables->days, array('data' => json_encode($data),
                                                                       'price_min' => $price_min,
                                                                       'price_max' => $price_max), 
                                                                 array('calendar_id' => $calendar_new_id,
                                                                       'day' => $day));
                        }
                        else{
                            array_push($query_insert_values, '(\''.$calendar_new_id.'_'.$day.'\', \''.$calendar_new_id.'\', \''.$day.'\', \''.$day_items[0].'\', \''.json_encode($data).'\', \''.$price_min.'\', \''.$price_max.'\')');
                        }
                        next($schedule);                        
                    }

                    if (count($query_insert_values) > 0){
                        $wpdb->query('INSERT INTO '.$DOPBSP->tables->days.' (unique_key, calendar_id, day, year, data, price_min, price_max) VALUES '.implode(', ', $query_insert_values));
                    }

                    $DOPBSP->classes->backend_calendar_schedule->clean();
                    $DOPBSP->classes->backend_calendar_schedule->setMaxYear($calendar_new_id);
                    $DOPBSP->classes->backend_calendar_schedule->setPrice($calendar_new_id);
                    $DOPBSP->classes->backend_calendar_schedule->setAvailable($calendar_new_id);
                    $DOPBSP->classes->backend_calendar_availability->set($calendar_new_id,
                                                                         $days);
                }
                
                /*
                 * Display new calendars list.
                 */
                $this->display();

            	die();
            }
            
            /*
             * Edit calendar.
             * 
             * @post field (string): calendars table field
             * @post id (integer): calendar ID
             * @post value (string): the value with which the field will be updated
             */
            function edit(){
                global $wpdb;
                global $DOPBSP;
                
                $field = $_POST['field'];
                $id = $_POST['id'];
                $value = $_POST['value'];
                
                /*
                 * Update calendar field.
                 */
                $wpdb->update($DOPBSP->tables->calendars, array($field => $value), 
                                                          array('id' => $id));
                
                die();
            }

            /*
             * Delete calendar.
             * 
             * @post id (integer): calendar ID
             * 
             * @return number of calendars left
             */
            function delete(){
                global $wpdb;
                global $DOPBSP;

                $id = $_POST['id'];
                
                /*
                 * Delete calendar.
                 */
                $wpdb->delete($DOPBSP->tables->calendars, array('id' => $id));
                
                /*
                 * Delete calendar schedule.
                 */
                $wpdb->delete($DOPBSP->tables->days, array('calendar_id' => $id));
                
                /*
                 * Delete calendar reservations.
                 */
                $wpdb->delete($DOPBSP->tables->reservations, array('calendar_id' => $id));
                
                /*
                 * Delete calendar settings.
                 */
                $wpdb->delete($DOPBSP->tables->settings_calendar, array('calendar_id' => $id));
                $wpdb->delete($DOPBSP->tables->settings_notifications, array('calendar_id' => $id));
                $wpdb->delete($DOPBSP->tables->settings_payment, array('calendar_id' => $id));
                
                /*
                 * Delete users permissions.
                 */
                $users = get_users();
                
                foreach ($users as $user){
                    if ($DOPBSP->classes->backend_settings_users->permission($user->ID, 'use-calendar', $id)){
                        $DOPBSP->classes->backend_settings_users->set(array('calendar_id' => $id,
                                                                            'id' => $user->ID,
                                                                            'slug' => '',
                                                                            'value' => 0));
                    }
                }
                
                /*
                 * Count the number of remaining calendars.
                 */
                $calendars = $wpdb->get_results('SELECT * FROM '.$DOPBSP->tables->calendars.' ORDER BY id DESC');
                
                echo $wpdb->num_rows;

            	die();
            }
            
            /*
             * Get calendar maximum available year.
             * 
             * @post id (integer): calendar ID
             * 
             * @return maximum available year
             */
            function getMaxYear($id){
                global $wpdb;
                global $DOPBSP;
                
                $calendar = $wpdb->get_row($wpdb->prepare('SELECT max_year FROM '.$DOPBSP->tables->calendars.' WHERE id=%d',
                                                          $id));
                
                return (int)($calendar->max_year == 0 ? $DOPBSP->classes->backend_settings->value($id, 'calendar', 'max_year'):$calendar->max_year);
            }
            
            /*
             * Set calendar maximum available year.
             * 
             * @post id (integer): calendar ID
             */
            function setMaxYear($id){
                global $wpdb;
                global $DOPBSP;
                
                $max_year = $wpdb->get_row($wpdb->prepare('SELECT MAX(year) AS year FROM '.$DOPBSP->tables->days.' WHERE calendar_id=%d',
                                                          $id)); 

                $wpdb->update($DOPBSP->tables->calendars, array('max_year' => $max_year->year > 0 ? $max_year->year:date('Y')), 
                                                          array('id' => $id));
            }
            
            /*
             * Set prices for faster search.
             * 
             * @post id (integer): calendar ID
             */
            function setPrice($id){
                global $wpdb;
                global $DOPBSP;
                
                $calendar = $wpdb->get_row($wpdb->prepare('SELECT MIN(price_min) AS price_min, MAX(price_max) AS price_max FROM '.$DOPBSP->tables->days.' WHERE calendar_id=%d AND price_min<>0',
                                                          $id)); 
                
                $wpdb->update($DOPBSP->tables->calendars, array('price_min' => $wpdb->num_rows == 0 ? 0:$calendar->price_min,
                                                                'price_max' => $wpdb->num_rows == 0 ? 0:$calendar->price_max), 
                                                          array('id' => $id));
            }
            
            /*
             * Set min available for faster search.
             * 
             * @post id (integer): calendar ID
             */
            function setAvailable($id){
                global $wpdb;
                global $DOPBSP;
                
                $min_available = 0;
                
                $calendar_data = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$DOPBSP->tables->calendars.' WHERE id=%d',
                                                               $id));
                
                if($wpdb->num_rows > 0) {
                    $calendar_data = json_decode($calendar_data->default_availability);
                    $min_available = ($calendar_data->available > 0 && $calendar_data->status != 'booked' && $calendar_data->status != 'unavailable' && $calendar_data->status != 'none') ?  $calendar_data->available:0;
                } 
                
                $wpdb->update($DOPBSP->tables->calendars, array('min_available' => $min_available), 
                                                          array('id' => $id));
            }
        }
    }