<?php

/**
 * 1. Load Child Theme CSS
 */
add_action('wp_enqueue_scripts', 'porto_child_css', 1001);
function porto_child_css()
{
    // porto child theme styles
    wp_deregister_style('styles-child');
    wp_register_style('styles-child', esc_url(get_stylesheet_directory_uri()) . '/style.css');
    wp_enqueue_style('styles-child');

    if (is_rtl()) {
        wp_deregister_style('styles-child-rtl');
        wp_register_style('styles-child-rtl', esc_url(get_stylesheet_directory_uri()) . '/style_rtl.css');
        wp_enqueue_style('styles-child-rtl');
    }
} // <-- THIS BRACE WAS MISSING! It closes the CSS function so the rest of the file works.


/**
 * 2. Force Georgian Regions
 */
add_filter('woocommerce_states', 'surneli_force_georgian_regions');
function surneli_force_georgian_regions($states)
{
    $states['GE'] = array(
        'TB' => 'თბილისი',
        'AJ' => 'აჭარა',
        'IM' => 'იმერეთი',
        'KA' => 'კახეთი',
        'SZ' => 'სამეგრელო',
        'GU' => 'გურია',
        'KK' => 'ქვემო ქართლი',
        'SK' => 'შიდა ქართლი',
        'MT' => 'მცხეთა-მთიანეთი',
        'RL' => 'რაჭა-ლეჩხუმი',
        'SJ' => 'სამცხე-ჯავახეთი'
    );
    return $states;
}

/**
 * 3. Force WooCommerce to make the City field a Select dropdown permanently
 */
add_filter('woocommerce_default_address_fields', 'surneli_force_city_select_native');
function surneli_force_city_select_native($fields)
{
    $fields['city']['type'] = 'select'; // Stops WooCommerce from turning it into a text box
    $fields['city']['options'] = array('' => 'აირჩიეთ ლოკაცია...');
    return $fields;
}

/**
 * 4. Update the Dropdown Options dynamically with SelectWoo UI
 */
/*add_action('wp_footer', 'surneli_checkout_city_logic');
function surneli_checkout_city_logic()
{
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                var cityMap = {
                    'TB': { 'TBI1': 'აბანოთუბანი', 'TBI3': 'აეროპორტი', 'TBI4': 'ავლაბარი', 'TBI5': 'ავჭალა', 'TBI2': 'აფრიკა', 'TBI6': 'ბაგები', 'TBI7': 'გლდანი', 'TBI8': 'გლდანულა', 'TBI9': 'დამპალო', 'TBI10': 'დიდი დიღომი', 'TBI11': 'დიდუბე', 'TBI13': 'დიღმის მასივი', 'TBI14': 'ვაზისუბანი', 'TBI15': 'ვაკე', 'TBI16': 'ვარკეთილი', 'TBI17': 'ვაშლიჯვარი', 'TBI18': 'ვერა', 'TBI19': 'ვეძისი', 'TBI20': 'ზაჰესი', 'TBI21': 'ზღვისუბანი', 'TBI22': 'თემქა', 'TBI23': 'თხინვალი', 'TBI24': 'ისანი', 'TBI58': 'კაკლები', 'TBI25': 'კიკეთი', 'TBI26': 'კოჯორი', 'TBI28': 'კრწანისი', 'TBI27': 'კუკია', 'TBI29': 'ლილო', 'TBI30': 'ლისი', 'TBI31': 'ლოტკინი', 'TBI32': 'მე-8 ლეგიონი', 'TBI33': 'მეტრომშენი', 'TBI34': 'მთაწმინდა', 'TBI35': 'მუხიანი', 'TBI36': 'ნავთლუღი', 'TBI37': 'ნაძალადევი', 'TBI38': 'ნუცუბიძე', 'TBI39': 'ორთაჭალა', 'TBI40': 'ორხევი', 'TBI41': 'ოქროყანა', 'TBI57': 'პეპელა', 'TBI42': 'საბურთალო', 'TBI43': 'სამგორი', 'TBI44': 'სანზონა', 'TBI45': 'სვანეთისუბანი', 'TBI46': 'სოლოლაკი', 'TBI56': 'სოფელი დიღომი', 'TBI47': 'ტაბახმელა', 'TBI48': 'ფონიჭალა', 'TBI49': 'ქოშიგორა', 'TBI50': 'შინდისი', 'TBI51': 'ჩუღურეთი', 'TBI52': 'წავკისი', 'TBI53': 'წოდორეთი', 'TBI54': 'წყნეთი', 'TBI55': 'ხილიანი' },
                    'AJ': { 'ADJ1': 'ბათუმი', 'ADJ2': 'გონიო', 'ADJ3': 'კვირიკე', 'ADJ4': 'მახინჯაური', 'ADJ5': 'ოჩხამური', 'ADJ6': 'სარფი', 'ADJ16': 'სხვა - აჭარა', 'ADJ7': 'ქედა', 'ADJ8': 'ქობულეთი', 'ADJ9': 'შუახევი', 'ADJ10': 'ჩაქვი', 'ADJ11': 'ციხისძირი', 'ADJ13': 'წინსვლა', 'ADJ12': 'ხელვაჩაური', 'ADJ14': 'ხულო', 'ADJ15': 'ხუცაბანი' },
                    'GU': { 'GUR1': 'ასკანა', 'GUR2': 'აცანა', 'GUR3': 'აცანა', 'GUR4': 'ბახმარო', 'GUR5': 'გრიგოლეთი', 'GUR6': 'დვაბზუ', 'GUR7': 'ვაკიჯვარი', 'GUR8': 'ლაითური', 'GUR9': 'ლანჩხუთი', 'GUR10': 'ლესა', 'GUR11': 'ლიხაური', 'GUR12': 'მამათი', 'GUR13': 'მზიანი', 'GUR14': 'ნაბეღლავი', 'GUR15': 'ნაგომარი', 'GUR16': 'ნარუჯა', 'GUR17': 'ნატანები', 'GUR18': 'ნიგოითი', 'GUR19': 'ოზურგეთი', 'GUR23': 'საჯავახო', 'GUR22': 'სუფსა', 'GUR32': 'სხვა - გურია', 'GUR21': 'ურეკი', 'GUR24': 'ქვიანი', 'GUR25': 'შეკვეთილი', 'GUR26': 'შემოქმედი', 'GUR27': 'შრომა', 'GUR28': 'შუხუთი', 'GUR20': 'ჩოხატაური', 'GUR29': 'წყალწმინდა', 'GUR30': 'ჭყონაგორა', 'GUR31': 'ხიდისთავი' },
                    'IM': { 'IME1': 'არგვეთა', 'IME2': 'ბაღდათი', 'IME3': 'გეგუთი', 'IME4': 'გომი', 'IME5': 'გორდი', 'IME6': 'დიდი ჯიხაიში', 'IME7': 'დიმი', 'IME8': 'ვანი', 'IME9': 'ზესტაფონი', 'IME10': 'თერჯოლა', 'IME11': 'იანეთი', 'IME12': 'კორბოული', 'IME13': 'კულაში', 'IME15': 'მათხოჯი', 'IME14': 'მაღლაკი', 'IME16': 'საირმე', 'IME17': 'სამტრედია', 'IME18': 'საჩხერე', 'IME30': 'სხვა - იმერეთი', 'IME19': 'ტყიბული', 'IME20': 'უბისა', 'IME21': 'ფარცხანაყანევი', 'IME22': 'ქუთაისი', 'IME23': 'შორაპანი', 'IME24': 'შროშა', 'IME25': 'წყალტუბო', 'IME26': 'ჭიათურა', 'IME27': 'ჭოგნარი', 'IME28': 'ხარაგაული', 'IME29': 'ხონი' },
                    'KA': { 'KAX1': 'აკურა', 'KAX68': 'ალვანი', 'KAX2': 'ანაგა', 'KAX3': 'არაშენდა', 'KAX4': 'არხილოსკალო', 'KAX7': 'ახაშენი', 'KAX6': 'ახმეტა', 'KAX69': 'ახმეტის რაიონი', 'KAX8': 'ბადიაური', 'KAX9': 'ბაკურციხე', 'KAX10': 'ბოდბე', 'KAX11': 'ბოდბისხევი', 'KAX12': 'ბუშეტი', 'KAX13': 'გამარჯვება', 'KAX14': 'გომბორი', 'KAX15': 'გულგულა', 'KAX16': 'გურჯაანი', 'KAX72': 'გურჯაანის რაიონი', 'KAX17': 'დედოფლისწყარო', 'KAX70': 'დედოფლისწყაროს რაიონი', 'KAX18': 'ენისელი', 'KAX19': 'ვაზისუბანი', 'KAX20': 'ვანთა', 'KAX21': 'ვარდისუბანი', 'KAX22': 'ვაქირი', 'KAX23': 'ვაჩნაძიანი', 'KAX24': 'ველისციხე', 'KAX25': 'ვეჯინი', 'KAX26': 'ზემო მაღარო', 'KAX27': 'თეთრი წყლები', 'KAX28': 'თელავი', 'KAX74': 'თელავის რაიონი', 'KAX29': 'თოხლიაური', 'KAX30': 'იყალთo', 'KAX31': 'კალაური', 'KAX32': 'კარდენახი', 'KAX66': 'კაჭრეთი', 'KAX67': 'კახეთის სხვა სოფელი', 'KAX33': 'კისისხევი', 'KAX35': 'კოლაგი', 'KAX34': 'კონდოლი', 'KAX36': 'ლაგოდეხი', 'KAX75': 'ლაგოდეხის რაიონი', 'KAX37': 'მანავი', 'KAX38': 'მაღარო', 'KAX39': 'მაჩხაანი', 'KAX40': 'მელაანი', 'KAX41': 'მუკუზანი', 'KAX42': 'ნასამხრალი', 'KAX43': 'ნინოწმინდა', 'KAX44': 'ნუკრიანი', 'KAX45': 'პატარძეული', 'KAX46': 'რუისპირი', 'KAX47': 'საგარეჯო', 'KAX73': 'საგარეჯოს რაიონი', 'KAX48': 'სამრეკლო', 'KAX49': 'სართიჭალა', 'KAX50': 'საქობო', 'KAX51': 'სიღნაღი', 'KAX52': 'უჯარმა', 'KAX53': 'ქვემო ხოდაშენი', 'KAX54': 'ყარაჯალა', 'KAX76': 'ყვარელი', 'KAX55': 'ყვარელი', 'KAX71': 'ყვარელის რაიონი', 'KAX56': 'შალაური', 'KAX57': 'შილდა', 'KAX58': 'ჩალაუბანი', 'KAX59': 'ჩუმლაყი', 'KAX60': 'ძირკოვი', 'KAX61': 'წინანდალი', 'KAX62': 'წნორი', 'KAX63': 'ჭანდარი', 'KAX64': 'ჯუგაანი' },
                    'MT': { 'MTS1': 'ანანური', 'MTS2': 'არხოტი', 'MTS3': 'გოროვანი', 'MTS4': 'დუშეთი', 'MTS5': 'თიანეთი', 'MTS6': 'მამკოდა', 'MTS7': 'მისაქციელი', 'MTS8': 'მუხრანი', 'MTS9': 'მცხეთა', 'MTS10': 'ნატახტარი', 'MTS11': 'ჟებოტა', 'MTS12': 'საგურამო', 'MTS13': 'სტეფანწმინდა', 'MTS23': 'სხვა - მცხეთა-მთიანეთი', 'MTS14': 'ფასანაური', 'MTS15': 'ქსოვრისი', 'MTS16': 'ჩარდახი', 'MTS17': 'ცხვარიჭამია', 'MTS18': 'ძეგვი', 'MTS19': 'ძველი ქანდა', 'MTS20': 'წეროვანი', 'MTS21': 'წილკანი', 'MTS22': 'წოდორეთი' },
                    'RL': { 'RAC1': 'ამბროლაური', 'RAC2': 'ლენტეხი', 'RAC3': 'ლეჩხუმი', 'RAC4': 'ნიკორწმინდა', 'RAC5': 'ონი', 'RAC9': 'სხვა - რაჭა-ლეჩხუმი', 'RAC6': 'ცაგერი', 'RAC7': 'წესი', 'RAC8': 'ხარისთვალა' },
                    'SZ': { 'SZS1': 'აბაშა', 'SZS2': 'ანაკლია', 'SZS3': 'ბულვანი', 'SZS4': 'განმუხური', 'SZS5': 'გაუწყინარი', 'SZS6': 'გეზათი', 'SZS7': 'გულეიკარი', 'SZS8': 'ეწერი', 'SZS9': 'ზუგდიდი', 'SZS10': 'ინგირი', 'SZS11': 'კახათი', 'SZS12': 'კეთილარი', 'SZS13': 'ლია', 'SZS14': 'მაიდანი', 'SZS15': 'მარანჭალა', 'SZS16': 'მარტველი', 'SZS17': 'მაცხოვრისკარი', 'SZS18': 'მესტია', 'SZS19': 'მიქავა', 'SZS20': 'ნაკიფუ', 'SZS21': 'ნორიო', 'SZS22': 'ნოსირი', 'SZS23': 'ობუჯი', 'SZS24': 'საბოკუჩავო', 'SZS25': 'საჩინო', 'SZS26': 'სენაკი', 'SZS27': 'სეფიეთი', 'SZS28': 'სუჯუნა', 'SZS44': 'სხვა', 'SZS29': 'ტყვირი', 'SZS30': 'ფოთი', 'SZS31': 'ჩხოროწყუ', 'SZS32': 'ცილორი', 'SZS33': 'ძიგური', 'SZS34': 'წალენჯიხა', 'SZS35': 'წყემი', 'SZS36': 'ჭითაწყარი', 'SZS37': 'ჭუბერი', 'SZS38': 'ხეთა', 'SZS39': 'ხობი', 'SZS40': 'ხცისი', 'SZS41': 'ჯგალი', 'SZS42': 'ჯვარზენი', 'SZS43': 'ჯვარი' },
                    'SJ': { 'JAV1': 'აბასთუმანი', 'JAV2': 'ადიგენი', 'JAV3': 'ასპინძა', 'JAV4': 'აწყური', 'JAV5': 'ახალდაბა', 'JAV6': 'ახალქალაქი', 'JAV7': 'ახალციხე', 'JAV8': 'ბაკურიანი', 'JAV9': 'ბორჯომი', 'JAV10': 'გიორგიწმინდა', 'JAV11': 'დვირი', 'JAV12': 'ვალე', 'JAV13': 'ვარძია', 'JAV14': 'თორი', 'JAV15': 'ივლიტა', 'JAV16': 'ლიბანი', 'JAV17': 'ლიკანი', 'JAV18': 'მზეთამზე', 'JAV19': 'მიტარბი', 'JAV20': 'მუგარეთი', 'JAV21': 'ნინოწმინდა', 'JAV22': 'სადგერი', 'JAV31': 'სხვა', 'JAV23': 'სხვილისისი', 'JAV24': 'ტბა', 'JAV25': 'ტიმოთესუბანი', 'JAV26': 'ცემი', 'JAV27': 'წაღვერი', 'JAV28': 'წინუბანი', 'JAV29': 'წნისი', 'JAV30': 'ჭობისხევი' },
                    'KK': { 'QQA1': 'ალგეთი', 'QQA2': 'ასურეთი', 'QQA3': 'ბედიანი', 'QQA4': 'ბოლნისი', 'QQA5': 'გამარჯვება', 'QQA6': 'გარდაბანი', 'QQA40': 'გარდაბნის რაიონი', 'QQA7': 'დმანისი', 'QQA8': 'ვაზიანი', 'QQA9': 'ზემო თელეთი', 'QQA10': 'თამარისი', 'QQA11': 'თეთრი წყარო', 'QQA12': 'თრიალეთი', 'QQA13': 'კაზრეთი', 'QQA14': 'კარალეთი', 'QQA15': 'კოდა', 'QQA16': 'კოლაგირი', 'QQA17': 'კუმისი', 'QQA18': 'მანგლისი', 'QQA19': 'მარნეული', 'QQA20': 'მარტყოფი', 'QQA21': 'ნაზარლო', 'QQA22': 'ნახიდური', 'QQA23': 'რუსთავი', 'QQA24': 'სადახლო', 'QQA39': 'სხვა', 'QQA25': 'ტალავერი', 'QQA26': 'ფარიზი', 'QQA27': 'ფარცხისი', 'QQA28': 'ფოლადაანთკარი', 'QQA29': 'ქესალო', 'QQA30': 'ქვემო თელეთი', 'QQA31': 'ყარაჯალა', 'QQA32': 'ყიზილაჯლო', 'QQA33': 'შაუმიანი', 'QQA34': 'ჩხიკვთა', 'QQA35': 'წალკა', 'QQA36': 'წინწყარო', 'QQA37': 'ხაიში', 'QQA38': 'ჯორჯიაშვილი' },
                    'SK': { 'SQA1': 'აბისი', 'SQA2': 'აგარა', 'SQA45': 'არადეთი', 'SQA3': 'აღაიანი', 'SQA4': 'ახალდაბა', 'SQA5': 'ახალშენი', 'SQA6': 'ბეღლეთი', 'SQA7': 'ბიჯნისი', 'SQA8': 'ბორჯომი', 'SQA9': 'ბრილი', 'SQA10': 'გიგანტი', 'SQA11': 'გომი', 'SQA12': 'გორი', 'SQA43': 'გორის რაიონი', 'SQA13': 'დვანისი', 'SQA14': 'ზანავი', 'SQA15': 'ზეკოტა', 'SQA16': 'თაგვეთი', 'SQA17': 'კავთისხევი', 'SQA18': 'კასპი', 'SQA44': 'კასპის რაიონი', 'SQA19': 'კეხიჯვარი', 'SQA20': 'ნადარბაზევი', 'SQA21': 'რუისი', 'SQA22': 'სურამი', 'SQA41': 'სხვა სოფელი', 'SQA23': 'ტაშისკარი', 'SQA24': 'ურბნისი', 'SQA25': 'ურთხვა', 'SQA26': 'უწლევი', 'SQA27': 'ქარელი', 'SQA42': 'ქარელის რაიონი', 'SQA28': 'ქვენატკოცა', 'SQA29': 'ქვიშხეთი', 'SQA30': 'ქსანი', 'SQA31': 'შავშვები', 'SQA32': 'ცოცხნარა', 'SQA33': 'ცხრამუხა', 'SQA34': 'წაბლოვანა', 'SQA36': 'წაღვლი', 'SQA35': 'წრომი', 'SQA37': 'ხაშური', 'SQA46': 'ხაშურის რაიონი', 'SQA38': 'ხელთუბანი', 'SQA39': 'ხვედურეთი', 'SQA40': 'ხიდისთავი' }
                };

                function updateSurneliCities(preserveSelection) {
                    var regionCode = $('#billing_state').val();
                    var $cityField = $('#billing_city');

                    // Grab the current selection before we wipe the options
                    var currentCityChoice = preserveSelection ? $cityField.val() : '';

                    if (cityMap[regionCode]) {
                        var options = '<option value="">აირჩიეთ ლოკაცია...</option>';
                        $.each(cityMap[regionCode], function (id, name) {
                           // options += '<option value="' + id + '">' + name + '</option>';
options += '<option value="' + id + '">' + name + '</option>';
//options += '<option value="' + name + '" data-code="' + id + '">' + name + '</option>';
                        });

                        // Rebuild the HTML
                        $cityField.html(options);

                        // Put the selection back!
                        if (currentCityChoice) {
                            $cityField.val(currentCityChoice);
                        }
                    } else {
                        $cityField.html('<option value="">აირჩიეთ ლოკაცია...</option>');
                    }

                    // Explicitly tell WooCommerce to apply the searchable UI
                    if ($.fn.selectWoo) {
                        $cityField.selectWoo({
                            width: '100%',
                            placeholder: 'აირჩიეთ ლოკაცია...'
                        });
                    } else if ($.fn.select2) {
                        $cityField.select2({
                            width: '100%',
                            placeholder: 'აირჩიეთ ლოკაცია...'
                        });
                    }
                }

                $(document.body).on('change', '#billing_state', function () {
                    // This forces WooCommerce to refresh the shipping methods
                    $(document.body).trigger('update_checkout');
                });

                // Add this inside your existing jQuery(function ($) { ... }); block
                $(document.body).on('change', '#billing_city', function () {
                    $(document.body).trigger('update_checkout');
                });

                // Run when Region changes manually (Don't preserve city, clear it)
                $(document.body).on('change', '#billing_state', function () {
                    updateSurneliCities(false);
                });

                // CRITICAL: Run after WooCommerce tries to update the checkout via AJAX (DO preserve city)
                $(document).ajaxComplete(function (event, xhr, settings) {
                    if (settings.url.indexOf('wc-ajax=update_order_review') > -1) {
                        updateSurneliCities(true);
                    }
                });

                // Fire on initial page load with a tiny delay so WooCommerce's native scripts finish first
                setTimeout(function () {
                    updateSurneliCities(true);
                }, 100);
            });
        </script>
        <?php
    }
}

add_filter('woocommerce_package_rates', 'surneli_apply_complex_shipping_rates', 100, 2);
function surneli_apply_complex_shipping_rates($rates, $package)
{
    $cart_total = WC()->cart->get_subtotal();
    $chosen_city_name = WC()->customer->get_billing_city();

    // 1. Get Selected City ID
    $chosen_city = '';
    if (isset($_POST['post_data'])) {
        parse_str($_POST['post_data'], $post_data);
        $chosen_city = isset($post_data['billing_city']) ? $post_data['billing_city'] : '';
    } elseif (isset($_POST['billing_city'])) {
        $chosen_city = $_POST['billing_city'];
    }

    // 2. Define Location Groups
    // TB Suburbs & Major Regional Cities (Tier 2)
    $tier2_locations = array(
        // TB Suburbs
        'TBI3',
        'TBI20',
        'TBI25',
        'TBI26',
        'TBI29',
        'TBI30',
        'TBI41',
        'TBI47',
        'TBI48',
        'TBI50',
        'TBI52',
        'TBI53',
        'TBI54',
        // Regional Hubs
        'ADJ1',
        'ADJ8',
        'IME22',
        'IME9',
        'IME17',
        'KAX28',
        'KAX47',
        'KAX62',
        'QQA23',
        'QQA19',
        'QQA4',
        'SQA12',
        'SQA37',
        'SZS9',
        'SZS30',
        'SZS26',
        'JAV9',
        'JAV7'
    );

    // 3. Determine Cost
    foreach ($rates as $rate_key => $rate) {
        if ('flat_rate' === $rate->method_id) {

            // Check if it's a Tier 2 Location (Suburbs/Cities)
            if (in_array($chosen_city, $tier2_locations)) {
                if ($cart_total >= 150) {
                    unset($rates[$rate_key]); // Handled by Free Shipping method
                } elseif ($cart_total >= 50) {
                    $rates[$rate_key]->cost = 4.00; // Discounted rate
                } else {
                    $rates[$rate_key]->cost = 8.00; // Standard rate
                }
            }

            // Check if it's a Tier 3 Location (Villages - starts with regional prefix and not in tier 2)
            elseif (!empty($chosen_city) && strpos($chosen_city, 'TBI') === false) {
                if ($cart_total >= 150) {
                    unset($rates[$rate_key]);
                } elseif ($cart_total >= 50) {
                    $rates[$rate_key]->cost = 7.00; // Discounted rate
                } else {
                    $rates[$rate_key]->cost = 12.00; // Standard rate
                }
            }

            // Tier 1 (Tbilisi Center) - Defaulting to your Zone setting of 5 GEL
        }
    }

    return $rates;
}
*/
/* GEMINI FIX for Above code */
/**
 * 1. Unified Surneli City Map Helper
 * This stores the data in one place for both the dropdown and the shipping logic.
 */
function get_surneli_city_map() {
    return array(
        'TB' => array( 'TBI1' => 'აბანოთუბანი', 'TBI3' => 'აეროპორტი', 'TBI4' => 'ავლაბარი', 'TBI5' => 'ავჭალა', 'TBI2' => 'აფრიკა', 'TBI6' => 'ბაგები', 'TBI7' => 'გლდანი', 'TBI8' => 'გლდანულა', 'TBI9' => 'დამპალო', 'TBI10' => 'დიდი დიღომი', 'TBI11' => 'დიდუბე', 'TBI13' => 'დიღმის მასივი', 'TBI14' => 'ვაზისუბანი', 'TBI15' => 'ვაკე', 'TBI16' => 'ვარკეთილი', 'TBI17' => 'ვაშლიჯვარი', 'TBI18' => 'ვერა', 'TBI19' => 'ვეძისი', 'TBI20' => 'ზაჰესი', 'TBI21' => 'ზღვისუბანი', 'TBI22' => 'თემქა', 'TBI23' => 'თხინვალი', 'TBI24' => 'ისანი', 'TBI58' => 'კაკლები', 'TBI25' => 'კიკეთი', 'TBI26' => 'კოჯორი', 'TBI28' => 'კრწანისი', 'TBI27' => 'კუკია', 'TBI29' => 'ლილო', 'TBI30' => 'ლისი', 'TBI31' => 'ლოტკინი', 'TBI32' => 'მე-8 ლეგიონი', 'TBI33' => 'მეტრომშენი', 'TBI34' => 'მთაწმინდა', 'TBI35' => 'მუხიანი', 'TBI36' => 'ნავთლუღი', 'TBI37' => 'ნაძალადევი', 'TBI38' => 'ნუცუბიძე', 'TBI39' => 'ორთაჭალა', 'TBI40' => 'ორხევი', 'TBI41' => 'ოქროყანა', 'TBI57' => 'პეპელა', 'TBI42' => 'საბურთალო', 'TBI43' => 'სამგორი', 'TBI44' => 'სანზონა', 'TBI45' => 'სვანეთისუბანი', 'TBI46' => 'სოლოლაკი', 'TBI56' => 'სოფელი დიღომი', 'TBI47' => 'ტაბახმელა', 'TBI48' => 'ფონიჭალა', 'TBI49' => 'ქოშიგორა', 'TBI50' => 'შინდისი', 'TBI51' => 'ჩუღურეთი', 'TBI52' => 'წავკისი', 'TBI53' => 'წოდორეთი', 'TBI54' => 'წყნეთი', 'TBI55' => 'ხილიანი' ),
        'AJ' => array( 'ADJ1' => 'ბათუმი', 'ADJ2' => 'გონიო', 'ADJ3' => 'კვირიკე', 'ADJ4' => 'მახინჯაური', 'ADJ5' => 'ოჩხამური', 'ADJ6' => 'სარფი', 'ADJ16' => 'სხვა - აჭარა', 'ADJ7' => 'ქედა', 'ADJ8' => 'ქობულეთი', 'ADJ9' => 'შუახევი', 'ADJ10' => 'ჩაქვი', 'ADJ11' => 'ციხისძირი', 'ADJ13' => 'წინსვლა', 'ADJ12' => 'ხელვაჩაური', 'ADJ14' => 'ხულო', 'ADJ15' => 'ხუცაბანი' ),
        'GU' => array( 'GUR1' => 'ასკანა', 'GUR2' => 'აცანა', 'GUR3' => 'აცანა', 'GUR4' => 'ბახმარო', 'GUR5' => 'გრიგოლეთი', 'GUR6' => 'დვაბზუ', 'GUR7' => 'ვაკიჯვარი', 'GUR8' => 'ლაითური', 'GUR9' => 'ლანჩხუთი', 'GUR10' => 'ლესა', 'GUR11' => 'ლიხაური', 'GUR12' => 'მამათი', 'GUR13' => 'მზიანი', 'GUR14' => 'ნაბეღლავი', 'GUR15' => 'ნაგომარი', 'GUR16' => 'ნარუჯა', 'GUR17' => 'ნატანები', 'GUR18' => 'ნიგოითი', 'GUR19' => 'ოზურგეთი', 'GUR23' => 'საჯავახო', 'GUR22' => 'სუფსა', 'GUR32' => 'სხვა - გურია', 'GUR21' => 'ურეკი', 'GUR24' => 'ქვიანი', 'GUR25' => 'შეკვეთილი', 'GUR26' => 'შემოქმედი', 'GUR27' => 'შრომა', 'GUR28' => 'შუხუთი', 'GUR20' => 'ჩოხატაური', 'GUR29' => 'წყალწმინდა', 'GUR30' => 'ჭყონაგორა', 'GUR31' => 'ხიდისთავი' ),
        'IM' => array( 'IME1' => 'არგვეთა', 'IME2' => 'ბაღდათი', 'IME3' => 'გეგუთი', 'IME4' => 'გომი', 'IME5' => 'გორდი', 'IME6' => 'დიდი ჯიხაიში', 'IME7' => 'დიმი', 'IME8' => 'ვანი', 'IME9' => 'ზესტაფონი', 'IME10' => 'თერჯოლა', 'IME11' => 'იანეთი', 'IME12' => 'კორბოული', 'IME13' => 'კულაში', 'IME15' => 'მათხოჯი', 'IME14' => 'მაღლაკი', 'IME16' => 'საირმე', 'IME17' => 'სამტრედია', 'IME18' => 'საჩხერე', 'IME30' => 'სხვა - იმერეთი', 'IME19' => 'ტყიბული', 'IME20' => 'უბისა', 'IME21' => 'ფარცხანაყანევი', 'IME22' => 'ქუთაისი', 'IME23' => 'შორაპანი', 'IME24' => 'შროშა', 'IME25' => 'წყალტუბო', 'IME26' => 'ჭიათურა', 'IME27' => 'ჭოგნარი', 'IME28' => 'ხარაგაული', 'IME29' => 'ხონი' ),
        'KA' => array( 'KAX1' => 'აკურა', 'KAX68' => 'ალვანი', 'KAX2' => 'ანაგა', 'KAX3' => 'არაშენდა', 'KAX4' => 'არხილოსკალო', 'KAX7' => 'ახაშენი', 'KAX6' => 'ახმეტა', 'KAX69' => 'ახმეტის რაიონი', 'KAX8' => 'ბადიაური', 'KAX9' => 'ბაკურციხე', 'KAX10' => 'ბოდბე', 'KAX11' => 'ბოდბისხევი', 'KAX12' => 'ბუშეტი', 'KAX13' => 'გამარჯვება', 'KAX14' => 'გომბორი', 'KAX15' => 'გულგულა', 'KAX16' => 'გურჯაანი', 'KAX72' => 'გურჯაანის რაიონი', 'KAX17' => 'დედოფლისწყარო', 'KAX70' => 'დედოფლისწყაროს რაიონი', 'KAX18' => 'ენისელი', 'KAX19' => 'ვაზისუბანი', 'KAX20' => 'ვანთა', 'KAX21' => 'ვარდისუბანი', 'KAX22' => 'ვაქირი', 'KAX23' => 'ვაჩნაძიანი', 'KAX24' => 'ველისციხე', 'KAX25' => 'ვეჯინი', 'KAX26' => 'ზემო მაღარო', 'KAX27' => 'თეთრი წყლები', 'KAX28' => 'თელავი', 'KAX74' => 'თელავის რაიონი', 'KAX29' => 'თოხლიაური', 'KAX30' => 'იყალთo', 'KAX31' => 'კალაური', 'KAX32' => 'კარდენახი', 'KAX66' => 'კაჭრეთი', 'KAX67' => 'კახეთის სხვა სოფელი', 'KAX33' => 'კისისხევი', 'KAX35' => 'კოლაგი', 'KAX34' => 'კონდოლი', 'KAX36' => 'ლაგოდეხი', 'KAX75' => 'ლაგოდეხის რაიონი', 'KAX37' => 'მანავი', 'KAX38' => 'მაღარო', 'KAX39' => 'მაჩხაანი', 'KAX40' => 'მელაანი', 'KAX41' => 'მუკუზანი', 'KAX42' => 'ნასამხრალი', 'KAX43' => 'ნინოწმინდა', 'KAX44' => 'ნუკრიანი', 'KAX45' => 'პატარძეული', 'KAX46' => 'რუისპირი', 'KAX47' => 'საგარეჯო', 'KAX73' => 'საგარეჯოს რაიონი', 'KAX48' => 'სამრეკლო', 'KAX49' => 'სართიჭალა', 'KAX50' => 'საქობო', 'KAX51' => 'სიღნაღი', 'KAX52' => 'უჯარმა', 'KAX53' => 'ქვემო ხოდაშენი', 'KAX54' => 'ყარაჯალა', 'KAX76' => 'ყვარელი', 'KAX55' => 'ყვარელი', 'KAX71' => 'ყვარელის რაიონი', 'KAX56' => 'შალაური', 'KAX57' => 'შილდა', 'KAX58' => 'ჩალაუბანი', 'KAX59' => 'ჩუმლაყი', 'KAX60' => 'ძირკოვი', 'KAX61' => 'წინანდალი', 'KAX62' => 'წნორი', 'KAX63' => 'ჭანდარი', 'KAX64' => 'ჯუგაანი' ),
        'MT' => array( 'MTS1' => 'ანანური', 'MTS2' => 'არხოტი', 'MTS3' => 'გოროვანი', 'MTS4' => 'დუშეთი', 'MTS5' => 'თიანეთი', 'MTS6' => 'მამკოდა', 'MTS7' => 'მისაქციელი', 'MTS8' => 'მუხრანი', 'MTS9' => 'მცხეთა', 'MTS10' => 'ნატახტარი', 'MTS11' => 'ჟებოტა', 'MTS12' => 'საგურამო', 'MTS13' => 'სტეფანწმინდა', 'MTS23' => 'სხვა - მცხეთა-მთიანეთი', 'MTS14' => 'ფასანაური', 'MTS15' => 'ქსოვრისი', 'MTS16' => 'ჩარდახი', 'MTS17' => 'ცხვარიჭამია', 'MTS18' => 'ძეგვი', 'MTS19' => 'ძველი ქანდა', 'MTS20' => 'წეროვანი', 'MTS21' => 'წილკანი', 'MTS22' => 'წოდორეთი' ),
        'RL' => array( 'RAC1' => 'ამბროლაური', 'RAC2' => 'ლენტეხი', 'RAC3' => 'ლეჩხუმი', 'RAC4' => 'ნიკორწმინდა', 'RAC5' => 'ონი', 'RAC9' => 'სხვა - რაჭა-ლეჩხუმი', 'RAC6' => 'ცაგერი', 'RAC7' => 'წესი', 'RAC8' => 'ხარისთვალა' ),
        'SZ' => array( 'SZS1' => 'აბაშა', 'SZS2' => 'ანაკლია', 'SZS3' => 'ბულვანი', 'SZS4' => 'განმუხური', 'SZS5' => 'გაუწყინარი', 'SZS6' => 'გეზათი', 'SZS7' => 'გულეიკარი', 'SZS8' => 'ეწერი', 'SZS9' => 'ზუგდიდი', 'SZS10' => 'ინგირი', 'SZS11' => 'კახათი', 'SZS12' => 'კეთილარი', 'SZS13' => 'ლია', 'SZS14' => 'მაიდანი', 'SZS15' => 'მარანჭალა', 'SZS16' => 'მარტველი', 'SZS17' => 'მაცხოვრისკარი', 'SZS18' => 'მესტია', 'SZS19' => 'მიქავა', 'SZS20' => 'ნაკიფუ', 'SZS21' => 'ნორიო', 'SZS22' => 'ნოსირი', 'SZS23' => 'ობუჯი', 'SZS24' => 'საბოკუჩავო', 'SZS25' => 'საჩინო', 'SZS26' => 'სენაკი', 'SZS27' => 'სეფიეთი', 'SZS28' => 'სუჯუნა', 'SZS44' => 'სხვა', 'SZS29' => 'ტყვირი', 'SZS30' => 'ფოთი', 'SZS31' => 'ჩხოროწყუ', 'SZS32' => 'ცილორი', 'SZS33' => 'ძიგური', 'SZS34' => 'წალენჯიხა', 'SZS35' => 'წყემი', 'SZS36' => 'ჭითაწყარი', 'SZS37' => 'ჭუბერი', 'SZS38' => 'ხეთა', 'SZS39' => 'ხობი', 'SZS40' => 'ხცისი', 'SZS41' => 'ჯგალი', 'SZS42' => 'ჯვარზენი', 'SZS43' => 'ჯვარი' ),
        'SJ' => array( 'JAV1' => 'აბასთუმანი', 'JAV2' => 'ადიგენი', 'JAV3' => 'ასპინძა', 'JAV4' => 'აწყური', 'JAV5' => 'ახალდაბა', 'JAV6' => 'ახალქალაქი', 'JAV7' => 'ახალციხე', 'JAV8' => 'ბაკურიანი', 'JAV9' => 'ბორჯომი', 'JAV10' => 'გიორგიწმინდა', 'JAV11' => 'დვირი', 'JAV12' => 'ვალე', 'JAV13' => 'ვარძია', 'JAV14' => 'თორი', 'JAV15' => 'ივლიტა', 'JAV16' => 'ლიბანი', 'JAV17' => 'ლიკანი', 'JAV18' => 'მზეთამზე', 'JAV19' => 'მიტარბი', 'JAV20' => 'მუგარეთი', 'JAV21' => 'ნინოწმინდა', 'JAV22' => 'სადგერი', 'JAV31' => 'სხვა', 'JAV23' => 'სხვილისისი', 'JAV24' => 'ტბა', 'JAV25' => 'ტიმოთესუბანი', 'JAV26' => 'ცემი', 'JAV27' => 'წაღვერი', 'JAV28' => 'წინუბანი', 'JAV29' => 'წნისი', 'JAV30' => 'ჭობისხევი' ),
        'KK' => array( 'QQA1' => 'ალგეთი', 'QQA2' => 'ასურეთი', 'QQA3' => 'ბედიანი', 'QQA4' => 'ბოლნისი', 'QQA5' => 'გამარჯვება', 'QQA6' => 'გარდაბანი', 'QQA40' => 'გარდაბნის რაიონი', 'QQA7' => 'დმანისი', 'QQA8' => 'ვაზიანი', 'QQA9' => 'ზემო თელეთი', 'QQA10' => 'თამარისი', 'QQA11' => 'თეთრი წყარო', 'QQA12' => 'თრიალეთი', 'QQA13' => 'კაზრეთი', 'QQA14' => 'კარალეთი', 'QQA15' => 'კოდა', 'QQA16' => 'კოლაგირი', 'QQA17' => 'კუმისი', 'QQA18' => 'მანგლისი', 'QQA19' => 'მარნეული', 'QQA20' => 'მარტყოფი', 'QQA21' => 'ნაზარლო', 'QQA22' => 'ნახიდური', 'QQA23' => 'რუსთავი', 'QQA24' => 'სადახლო', 'QQA39' => 'სხვა', 'QQA25' => 'ტალავერი', 'QQA26' => 'ფარიზი', 'QQA27' => 'ფარცხისი', 'QQA28' => 'ფოლადაანთკარი', 'QQA29' => 'ქესალო', 'QQA30' => 'ქვემო თელეთი', 'QQA31' => 'ყარაჯალა', 'QQA32' => 'ყიზილაჯლო', 'QQA33' => 'შაუმიანი', 'QQA34' => 'ჩხიკვთა', 'QQA35' => 'წალკა', 'QQA36' => 'წინწყარო', 'QQA37' => 'ხაიში', 'QQA38' => 'ჯორჯიაშვილი' ),
        'SK' => array( 'SQA1' => 'აბისი', 'SQA2' => 'აგარა', 'SQA45' => 'არადეთი', 'SQA3' => 'აღაიანი', 'SQA4' => 'ახალდაბა', 'SQA5' => 'ახალშენი', 'SQA6' => 'ბეღლეთი', 'SQA7' => 'ბიჯნისი', 'SQA8' => 'ბორჯომი', 'SQA9' => 'ბრილი', 'SQA10' => 'გიგანტი', 'SQA11' => 'გომი', 'SQA12' => 'გორი', 'SQA43' => 'გორის რაიონი', 'SQA13' => 'დვანისი', 'SQA14' => 'ზანავი', 'SQA15' => 'ზეკოტა', 'SQA16' => 'თაგვეთი', 'SQA17' => 'კავთისხევი', 'SQA18' => 'კასპი', 'SQA44' => 'კასპის რაიონი', 'SQA19' => 'კეხიჯვარი', 'SQA20' => 'ნადარბაზევი', 'SQA21' => 'რუისი', 'SQA22' => 'სურამი', 'SQA41' => 'სხვა სოფელი', 'SQA23' => 'ტაშისკარი', 'SQA24' => 'ურბნისი', 'SQA25' => 'ურთხვა', 'SQA26' => 'უწლევი', 'SQA27' => 'ქარელი', 'SQA42' => 'ქარელის რაიონი', 'SQA28' => 'ქვენატკოცა', 'SQA29' => 'ქვიშხეთი', 'SQA30' => 'ქსანი', 'SQA31' => 'შავშვები', 'SQA32' => 'ცოცხნარა', 'SQA33' => 'ცხრამუხა', 'SQA34' => 'წაბლოვანა', 'SQA36' => 'წაღვლი', 'SQA35' => 'წრომი', 'SQA37' => 'ხაშური', 'SQA46' => 'ხაშურის რაიონი', 'SQA38' => 'ხელთუბანი', 'SQA39' => 'ხვედურეთი', 'SQA40' => 'ხიდისთავი' )
    );
}

/**
 * 2. Update the Dropdown Options dynamically (Keeps the Georgian Names as values)
 */
add_action('wp_footer', 'surneli_checkout_city_logic');
function surneli_checkout_city_logic() {
    if (is_checkout()) {
        $php_city_map = get_surneli_city_map();
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                // Read the map securely from PHP
                var cityMap = <?php echo json_encode($php_city_map); ?>;

                function updateSurneliCities(preserveSelection) {
                    var regionCode = $('#billing_state').val();
                    var $cityField = $('#billing_city');
                    var currentCityChoice = preserveSelection ? $cityField.val() : '';

                    if (cityMap[regionCode]) {
                        var options = '<option value="">აირჩიეთ ლოკაცია...</option>';
                        $.each(cityMap[regionCode], function (id, name) {
                           // Keeps the name as the value so the customer order looks pretty
                           options += '<option value="' + name + '">' + name + '</option>';
                        });

                        $cityField.html(options);

                        if (currentCityChoice) {
                            $cityField.val(currentCityChoice);
                        }
                    } else {
                        $cityField.html('<option value="">აირჩიეთ ლოკაცია...</option>');
                    }

                    if ($.fn.selectWoo) {
                        $cityField.selectWoo({ width: '100%', placeholder: 'აირჩიეთ ლოკაცია...' });
                    } else if ($.fn.select2) {
                        $cityField.select2({ width: '100%', placeholder: 'აირჩიეთ ლოკაცია...' });
                    }
                }

                $(document.body).on('change', '#billing_state', function () {
                    $(document.body).trigger('update_checkout');
                    updateSurneliCities(false);
                });

                $(document.body).on('change', '#billing_city', function () {
                    $(document.body).trigger('update_checkout');
                });

                $(document).ajaxComplete(function (event, xhr, settings) {
                    if (settings.url.indexOf('wc-ajax=update_order_review') > -1) {
                        updateSurneliCities(true);
                    }
                });

                setTimeout(function () {
                    updateSurneliCities(true);
                }, 100);
            });
        </script>
        <?php
    }
}

/**
 * 3. Complex Shipping Logic with Reverse-Lookup
 */
add_filter('woocommerce_package_rates', 'surneli_apply_complex_shipping_rates', 100, 2);
function surneli_apply_complex_shipping_rates($rates, $package) {
    $cart_total = WC()->cart->get_subtotal();
    
    // WooCommerce gives us the beautiful name (e.g. "აბანოთუბანი")
    $chosen_city_name = WC()->customer->get_billing_city();
    
    // We reverse-lookup the name to find the hidden ID (e.g. "TBI1")
    $city_map = get_surneli_city_map();
    $found_id = '';
    foreach ($city_map as $region => $cities) {
        if (in_array($chosen_city_name, $cities)) {
            $found_id = array_search($chosen_city_name, $cities);
            break;
        }
    }

    $tier2_locations = array('TBI3', 'TBI20', 'TBI25', 'TBI26', 'TBI29', 'TBI30', 'TBI41', 'TBI47', 'TBI48', 'TBI50', 'TBI52', 'TBI53', 'TBI54', 'ADJ1', 'ADJ8', 'IME22', 'IME9', 'IME17', 'KAX28', 'KAX47', 'KAX62', 'QQA23', 'QQA19', 'QQA4', 'SQA12', 'SQA37', 'SZS9', 'SZS30', 'SZS26', 'JAV9', 'JAV7');

    foreach ($rates as $rate_key => $rate) {
        if ('flat_rate' === $rate->method_id) {
            
            // Tier 2 
            if (in_array($found_id, $tier2_locations)) {
                if ($cart_total >= 150) {
                    unset($rates[$rate_key]);
                } else {
                    $rates[$rate_key]->cost = ($cart_total >= 50) ? 4.00 : 8.00;
                }
            } 
            // Tier 1 (Tbilisi Center) - Check if the hidden ID starts with 'TBI'
            elseif (strpos($found_id, 'TBI') === 0) {
                if ($cart_total >= 150) {
                    unset($rates[$rate_key]);
                } else {
                    $rates[$rate_key]->cost = 6.00; // Success! Forcing 5 GEL.
                }
            } 
            // Tier 3 (Villages and everything else)
            elseif (!empty($chosen_city_name)) {
                if ($cart_total >= 150) {
                    unset($rates[$rate_key]);
                } else {
                    $rates[$rate_key]->cost = ($cart_total >= 50) ? 7.00 : 12.00;
                }
            }
        }
    }

    return $rates;
}

add_filter('woocommerce_checkout_fields', 'surneli_custom_checkout_fields', 9999);
function surneli_custom_checkout_fields($fields)
{

    // Rename labels
    $fields['billing']['billing_state']['label'] = 'რეგიონი';
    $fields['billing']['billing_city']['label'] = 'ქალაქი/დაბა';
    $fields['billing']['billing_address_1']['label'] = 'მისამართი (ქუჩა, კორპუსი, ბინა)';
    $fields['billing']['billing_address_1']['placeholder'] = 'მაგ: ჭავჭავაძის გამზ. 12, ბინა 4';
    $fields['billing']['billing_phone']['label'] = 'ტელეფონის ნომერი';

    // Postcode logic
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['required'] = false;
        $fields['billing']['billing_email']['required'] = false;
    }

    // Unset unused fields
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_2']);
    // unset($fields['billing']['billing_email']);


    // REORDER: Now that CSS isn't fighting us, this will work visually
    $fields['billing']['billing_first_name']['priority'] = 10;
    $fields['billing']['billing_last_name']['priority'] = 20;
    $fields['billing']['billing_phone']['priority'] = 25;

    $fields['billing']['billing_country']['priority'] = 35; // Hidden via CSS
    $fields['billing']['billing_state']['priority'] = 31;
    $fields['billing']['billing_city']['priority'] = 32;
    $fields['billing']['billing_address_1']['priority'] = 60;
    $fields['billing']['billing_postcode']['priority'] = 65; // Hidden via CSS

    return $fields;
}

/**
 * 8. Force the Priority of Core Address Fields
 */
add_filter('woocommerce_default_address_fields', 'surneli_force_address_order', 9999);
function surneli_force_address_order($fields)
{

    // Force CSS classes to ensure they stack vertically and don't get trapped in a CSS grid
    $fields['country']['class'] = array('form-row-wide');
    $fields['state']['class'] = array('form-row-wide');
    $fields['city']['class'] = array('form-row-wide');
    $fields['address_1']['class'] = array('form-row-wide');
    $fields['postcode']['class'] = array('form-row-wide');

    // Lock in the exact priorities (31, 32, etc.)
    $fields['country']['priority'] = 30; // Hidden by CSS
    $fields['state']['priority'] = 31; // Region
    $fields['city']['priority'] = 32; // City
    $fields['address_1']['priority'] = 33; // Street Address
    $fields['postcode']['priority'] = 34; // Hidden by CSS

    return $fields;
}

/**
 * 6. Strip requirements out of core default address fields
 */
add_filter('woocommerce_default_address_fields', 'surneli_override_default_address_fields', 9999);
function surneli_override_default_address_fields($address_fields)
{
    if (isset($address_fields['postcode'])) {
        $address_fields['postcode']['required'] = false;
    }
    return $address_fields;
}

/**
 * 7. The Postcode Interceptor 
 */
// NOTE: I kept your 'init' hook here as requested. If Flitt still gives you trouble, 
// we can swap this exact hook back to the 'wp_loaded' / 'Titanium' override we discussed.
add_action('init', 'surneli_absolute_nuclear_postcode');
function surneli_absolute_nuclear_postcode()
{
    // Check if this is the exact WooCommerce AJAX checkout request
    if (isset($_GET['wc-ajax']) && $_GET['wc-ajax'] === 'checkout') {
        // Directly modify the raw PHP $_POST global before ANY plugin or gateway can see it
        if (empty($_POST['billing_postcode'])) {
            $_POST['billing_postcode'] = '0100'; // Inject Tbilisi Zip Code silently
        }
        if (empty($_POST['shipping_postcode'])) {
            $_POST['shipping_postcode'] = '0100';
        }
    }
}

/**
 * Display the 1ml price range dynamically based on variation volumes
 *
add_filter('woocommerce_variable_price_html', 'surneli_range_price_per_ml', 10, 2);
function surneli_range_price_per_ml($price, $product)
{
    $min_per_ml = null;
    $max_per_ml = null;

    // Get all active variations for this specific fragrance
    $variations = $product->get_available_variations();

    foreach ($variations as $variation) {
        // Grab the actual front-end price of this specific variation
        $var_price = $variation['display_price'];

        $volume = 0;

        // Look through the attributes (e.g., "Size" or "Volume") to extract the number
        foreach ($variation['attributes'] as $key => $value) {
            // This grabs the first number it finds in the attribute (e.g., "10" from "10ml")
            if (preg_match('/(\d+)/', $value, $matches)) {
                $volume = (float) $matches[1];
                break;
            }
        }

        // If we found a valid volume and price, calculate the per-ml cost
        if ($volume > 0 && $var_price > 0) {
            $price_per_ml = $var_price / $volume;

            if (is_null($min_per_ml) || $price_per_ml < $min_per_ml) {
                $min_per_ml = $price_per_ml; // Set the cheapest (e.g., from 10ml)
            }
            if (is_null($max_per_ml) || $price_per_ml > $max_per_ml) {
                $max_per_ml = $price_per_ml; // Set the most expensive (e.g., from 2ml)
            }
        }
    }

    // If we successfully calculated the per ml prices, build the Georgian text string
    if (!is_null($min_per_ml) && !is_null($max_per_ml)) {

        // Format to 2 decimal places exactly as you requested
        $formatted_min = number_format($min_per_ml, 2, '.', '');
        $formatted_max = number_format($max_per_ml, 2, '.', '');

        // If the prices happen to be exactly the same, just show a single price
        if ($formatted_min === $formatted_max) {
            return '1 მლ – ' . $formatted_max . '₾';
        }

        // Return the exact layout you asked for!
        return '1 მლ – ' . $formatted_min . '₾-დან ' . $formatted_max . '₾-მდე';
    }

    // If something goes wrong or the product doesn't have variations, show default WooCommerce price
    return $price;
}
*/

/**

 * Display starting price based on the smallest available volume

 * Example:

 * 2ml variation exists  -> "12 ლარიდან"

 * only 5ml exists       -> "25 ლარიდან"

 * only 100ml exists     -> "180 ლარიდან"

 */

add_filter('woocommerce_variable_price_html', 'surneli_starting_price_by_smallest_volume', 10, 2);

function surneli_starting_price_by_smallest_volume($price, $product)

{

    $smallest_volume = null;

    $smallest_price = null;

    // Get all available variations

    $variations = $product->get_available_variations();

    foreach ($variations as $variation) {

        $var_price = $variation['display_price'];

        $volume = null;

        // Extract volume number from variation attributes

        foreach ($variation['attributes'] as $value) {

            // Matches: 2ml, 5 ml, 10ML etc.

            if (preg_match('/(\d+(\.\d+)?)/', $value, $matches)) {

                $volume = (float) $matches[1];

                break;

            }

        }

        // Skip invalid variations

        if (!$volume || !$var_price) {

            continue;

        }

        // Find smallest volume

        if (is_null($smallest_volume) || $volume < $smallest_volume) {

            $smallest_volume = $volume;

            $smallest_price = $var_price;

        }

    }

    // Return formatted text

    if (!is_null($smallest_price)) {

        // Remove decimals if whole number

        if ($smallest_price == floor($smallest_price)) {

            $smallest_price = number_format($smallest_price, 0);

        } else {

            $smallest_price = number_format($smallest_price, 2);

        }

        return $smallest_price . ' ლარიდან';

    }

    return $price;

}
/**
 * Append the detailed descriptive text dynamically to the selected variation price
 */
add_filter('woocommerce_available_variation', 'surneli_selected_variation_per_ml', 10, 3);
function surneli_selected_variation_per_ml($variation_data, $product, $variation)
{

    // Get the exact price of this specific variation
    $price = $variation->get_price();
    $volume = 0;

    // Extract the volume number from the variation attributes (e.g., "3" from "3მლ")
    $attributes = $variation->get_attributes();
    foreach ($attributes as $key => $value) {
        if (preg_match('/(\d+)/', $value, $matches)) {
            $volume = (float) $matches[1];
            break;
        }
    }

 // NEW: Check if volume is 100 or higher (or exactly 100)
    // This stops the function before any HTML is generated for this specific variation.
    if ($volume == 100) {
        return $variation_data;
    }

    // If we have a valid volume and price, do the math and build the sentence
    if ($volume > 0 && $price > 0) {
        $per_ml = $price / $volume;
        $formatted_per_ml = number_format($per_ml, 2, '.', '');

        // Build the text using a div for better spacing since it's a full sentence
        $per_ml_html = '<div class="surneli-variation-per-ml" style="font-size: 14px; color: #555; margin-top: 10px; line-height: 1.5;">';
        $per_ml_html .= 'თქვენ უკვეთავთ <strong>' . $volume . '</strong> მილილიტრ ორიგინალ სუნამოს შუშის ატომაიზერით. 1 მილილიტრის ღირებულება <strong>' . $formatted_per_ml . '</strong> ლარი.';
        $per_ml_html .= '</div>';

        // Attach it to the default WooCommerce price HTML
        $variation_data['price_html'] .= $per_ml_html;
    }

    return $variation_data;
}

add_filter('woocommerce_cart_needs_shipping', function ($needs_shipping) {

    if (is_cart()) {

        return false;

    }

    return $needs_shipping;

});


add_action('woocommerce_cart_totals_after_order_total', function () {

    if (is_cart()) {

        echo '<tr class="shipping-note">

                <th></th>

                <td style="padding-top:10px; color:#666; font-size:14px;">

მიტანის ღირებულება დაითვლება გადახდის გვერდზე
                </td>

              </tr>';

    }

});
add_filter('gettext', function ($translated, $text, $domain) {

    $translations = [
        'Shopping Cart'  => 'კალათა',
        'Shopping cart'  => 'კალათა',
        'Checkout'       => 'შეძენა',
        'Order Complete' => 'დადასტურება',
        'Order complete' => 'დადასტურება',
        'Order received' => 'შეკვეთა დასრულებულია',
    ];

    return $translations[$text] ?? $translated;

}, 999, 3);



add_filter('gettext', function ($translated, $text, $domain) {

    $translations = [
        'Have a coupon?' => 'გაქვთ ფასდაკლების პრომო კოდი?',
        'Click here to enter your code' => 'მაქვს ფასდაკლების კოდი',
        'Enter your code' => 'შეიყვანეთ თქვენი კოდი',
    ];

    return $translations[$text] ?? $translated;

}, 999, 3);
add_filter('gettext', function ($translated, $text, $domain) {

    if (strpos($text, 'get free shipping') !== false) {
        return 'დაამატეთ %s და მიიღეთ უფასო მიწოდება';
    }

    return $translated;

}, 999, 3);


add_filter('woocommerce_localisation_address_formats', function ($formats) {

    $formats['GE'] = "{name}\n{address_1}\n{city}";

    return $formats;
});



/**
 * Georgian phone validation for WooCommerce
 */
add_action('woocommerce_checkout_process', function () {

    $phone = isset($_POST['billing_phone'])
        ? preg_replace('/\s+/', '', $_POST['billing_phone'])
        : '';

    // Remove +995 if user enters it
    $phone = preg_replace('/^\+995/', '', $phone);

    // Accept only Georgian mobile numbers
    // Example: 555123456
    if (!preg_match('/^5\d{8}$/', $phone)) {

        wc_add_notice(
            'გთხოვთ შეიყვანოთ სწორი ქართული ნომერი (მაგ: 555123456)',
            'error'
        );
    }
});


/**
 * Add placeholder + maxlength
 */
add_filter('woocommerce_checkout_fields', function ($fields) {

    $fields['billing']['billing_phone']['placeholder'] = '5XX XXX XXX';

    $fields['billing']['billing_phone']['custom_attributes'] = [
        'maxlength'   => '9',
        'inputmode'   => 'numeric',
        'pattern'     => '5[0-9]{8}',
    ];

    return $fields;
});

function surneli_allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'surneli_allow_svg_upload');


add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if ($ext === 'svg') {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
    }

    return $data;

}, 10, 4);


add_filter('woocommerce_localisation_address_formats', function($formats) {

    // Custom Georgia format WITHOUT state/postcode
    $formats['GE'] = "{name}\n{city}, {address_1}";

    return $formats;

});

/**
 * Show product category before product title on single product page
 * Example: Women's Fragrance – Marwa
 */
add_filter('the_title', 'surneli_add_category_before_product_title', 10, 2);

function surneli_add_category_before_product_title($title, $post_id) {

    if (
        !is_admin() &&
        is_product() &&
        get_the_ID() === $post_id
    ) {

        $terms = get_the_terms($post_id, 'product_cat');

        if (!empty($terms) && !is_wp_error($terms)) {
            $category_name = $terms[0]->name;

            return $category_name . ' – ' . $title;
        }
    }

    return $title;
}
add_filter( 'porto_product_hover_image', '__return_false' );



add_filter( 'woocommerce_product_get_gallery_image_ids', 'surneli_disable_hover_image_fetch', 99, 2 );

function surneli_disable_hover_image_fetch( $image_ids, $product ) {
    // Check if we are NOT on a single product page.
    // This applies to shop, categories, and Elementor grids.
    if ( ! is_product() ) {
        // Return an empty array so Porto finds no second image to render
        return array(); 
    }
    
    // Otherwise, return the normal gallery images (for the single product page)
    return $image_ids;
}


/**
 * SMS Office Integration for surneli.ge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Surneli_SMS_Gateway {

	private static $api_key = '32667ee8d8534f08a436a67e52ef3b12';
	private static $sender  = 'Surneli.ge'; // შენი დამტკიცებული გამგზავნის სახელი

	/**
	 * ძირითადი ფუნქცია SMS-ის გასაგზავნად
	 */
	public static function send_sms($phone, $message) {
		if (empty($phone) || empty($message)) {
			return false;
		}

		// ნომრის ვალიდაცია/ფორმატირება ქართული ნომრებისთვის (995XXXXXXXXX)
		$phone = preg_replace('/[^0-9]/', '', $phone);

		if (strlen($phone) === 9 && strpos($phone, '5') === 0) {
			$phone = '995' . $phone;
		}

		// ენდპოინტის აწყობა
		$url = add_query_arg(
			array(
				'key'         => self::$api_key,
			'destination' => $phone,
			'sender'      => self::$sender,
			'content'     => rawurlencode($message),
			'urgent'      => 'true' // სურვილისამებრ, პრიორიტეტული გაგზავნისთვის
	),
		'https://smsoffice.ge/api/v2/send/'
	);

		// ნონ-ბლოკინგ მოთხოვნა, რომ კლიენტს Checkout გვერდი არ გაეჭედოს
		$response = wp_remote_get($url, array(
			'timeout'   => 5,
			'blocking'  => false, // შეცვალე false-ზე, თუ პასუხის ლოგირება არ გჭირდება
	));

		if (is_wp_error($response)) {
			// error_log('SMS Office Error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);

		// SMS Office წარმატებულ გაგზავნაზე აბრუნებს ციფრულ ID-ს (ინტეგერი > 0)
		if (is_numeric($body) && intval($body) > 0) {
			return true;
		}

		// error_log('SMS Office Failed. Response body: ' . $body);
		return false;
	}
}

/**
 * ჰუკები WooCommerce-ის სტატუსების მიხედვით
 */

// 1. ახალი შეკვეთისას (Processing სტატუსი - როცა კლიენტი იხდის ან ირჩევს ადგილზე გადახდას)
add_action('woocommerce_order_status_processing', 'surneli_sms_on_processing', 10, 1);
function surneli_sms_on_processing($order_id) {
	$order = wc_get_order($order_id);
	if (!$order) return;

	$phone = $order->get_billing_phone();
	$first_name = $order->get_billing_first_name();

	$message = sprintf(
		"გამარჯობა %s, თქვენი ორიგინალი სურნელის შეკვეთა(#%d) მივიღეთ და მუშავდება. მადლობა, Surneli.ge",
		$first_name,
		$order_id
	);

	Surneli_SMS_Gateway::send_sms($phone, $message);
}










// Remove native tracking shortcode and register our own
add_action( 'init', 'surneli_replace_order_tracking_shortcode' );
function surneli_replace_order_tracking_shortcode() {
    remove_shortcode( 'woocommerce_order_tracking' );
    add_shortcode( 'woocommerce_order_tracking', 'surneli_custom_order_tracking_handler' );
}

function surneli_custom_order_tracking_handler() {
    if ( is_null( WC()->cart ) ) return;

    $output = '';

    // Handle form submission
    if ( isset( $_POST['surneli_track_order_submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'surneli_track_order' ) ) {
        $order_id = isset( $_POST['orderid'] ) ? absint( $_POST['orderid'] ) : 0;
        $phone    = isset( $_POST['order_phone'] ) ? sanitize_text_field( $_POST['order_phone'] ) : '';

        $order = wc_get_order( $order_id );

        // Sanitize phone input numbers for loose matching
        $clean_input_phone = preg_replace('/[^0-9]/', '', $phone);
        $clean_order_phone = $order ? preg_replace('/[^0-9]/', '', $order->get_billing_phone()) : '';

        // Verification logic (Matching by Order ID and last 9 digits of phone)
        if ( $order && ! empty( $clean_input_phone ) && substr($clean_order_phone, -9) === substr($clean_input_phone, -9) ) {
            
            // ვიწყებთ ბუფერირებას, რადგან wc_get_template პირდაპირ აკეთებს echo-ს
            ob_start();
            wc_get_template( 'order/order-details.php', array( 'order_id' => $order_id ) );
            return ob_get_clean();
            
        } else {
            wc_print_notice( 'შეკვეთა ამ მონაცემებით ვერ მოიძებნა.', 'error' );
        }
    }

    // Render Form (action="" უზრუნველყოფს, რომ იქვე გაიგზავნოს მოთხოვნა)
    ob_start();
    ?>
    <form action="" method="post" class="track_order">
        <p><?php _e( 'თქვენი შეკვეთის მიკვლევისათვის, გთხოვთ შეიყვანოთ მონაცემები.', 'woocommerce' ); ?></p>
        
        <p class="form-row form-row-first">
            <label for="orderid"><?php _e( 'შეკვეთის იდენტიფიკატორი', 'woocommerce' ); ?></label>
            <input class="input-text" type="text" name="orderid" id="orderid" value="<?php echo isset( $_POST['orderid'] ) ? esc_attr( $_POST['orderid'] ) : ''; ?>" placeholder="მაგ: 11629" />
        </p>
        
        <p class="form-row form-row-last">
            <label for="order_phone"><?php _e( 'მობილურის ნომერი', 'woocommerce' ); ?></label>
            <input class="input-text" type="text" name="order_phone" id="order_phone" value="<?php echo isset( $_POST['order_phone'] ) ? esc_attr( $_POST['order_phone'] ) : ''; ?>" placeholder="5XXXXXXXX" />
        </p>
        <div class="clear"></div>

        <p class="form-row">
            <?php wp_nonce_field( 'surneli_track_order' ); ?>
            <button type="submit" class="button" name="surneli_track_order_submit" value="Track"><?php _e( 'თვალთვალი', 'woocommerce' ); ?></button>
        </p>
    </form>
    <?php
    return ob_get_clean();
}


add_action( 'woocommerce_after_add_to_cart_button', 'surneli_authenticity_box' );
function surneli_authenticity_box() {
    ?>
    <div class="surneli-authenticity-box">
        <div class="surneli-authenticity-box__icon">
            <!-- Replace with an actual badge/checkmark SVG or image asset -->
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#2e7d32" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path d="M8 12l2.5 2.5L16 9" />
            </svg>
        </div>
        <div>
            <div class="surneli-authenticity-box__title">ავთენტურობის გარანტია</div>
            <p>ჩვენს საიტზე განთავსებული ყველა სუნამო არის ორიგინალი</p>
            <a href="/authenticity" class="surneli-authenticity-box__link">შეიტყვეთ მეტი</a>
        </div>
    </div>
    <?php
}

/**
 * Remove the starting-price display on single product pages only.
 * This hook only fires on single product pages already, so it has
 * zero effect on Shop/Category archive pricing.
 */
add_action( 'wp', 'surneli_remove_single_product_price' );
function surneli_remove_single_product_price() {
    if ( is_product() ) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    }
}

/**
 * Add static "purchase by volume" note right under the title,
 * before the size/variation selector.
 */
add_action( 'woocommerce_single_product_summary', 'surneli_volume_purchase_note', 8 );
function surneli_volume_purchase_note() {
    echo '<p class="surneli-volume-note">მილილიტრობით შეძენის შემთხვევაში თქვენ ყიდულობთ ორიგინალ სუნამოს შუშის ატომაიზერში</p>';
}