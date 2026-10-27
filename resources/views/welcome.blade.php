<!DOCTYPE html>
<html lang="en">

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Multipurpose, super Feb, powerful, clean modern responsive bootstrap 5 admin template"
        name="description">
    <meta
        content="admin template,  admin template, dashboard template, flat admin template, responsive admin template, web app"
        name="keywords">
    <meta content="la-themes" name="author">
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/favicon.ico" rel="shortcut icon" type="image/x-icon">

    <title>Jivident 3D</title>
    <!--font-awesome-css-->
    <link href="{{ url('') }}/assets/vendor/fontawesome/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">

    <!-- iconoir icon css  -->
    <link href="{{ url('') }}/assets/vendor/ionio-icon/css/iconoir.css" rel="stylesheet">

    <!-- Animation css -->
    <link href="{{ url('') }}/assets/vendor/animation/animate.min.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ url('') }}/assets/vendor/tabler-icons/tabler-icons.css" rel="stylesheet" type="text/css">

    <!--flag Icon css-->
    <link href="{{ url('') }}/assets/vendor/flag-icons-master/flag-icon.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ url('') }}/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- apexcharts css-->
    <link href="{{ url('') }}/assets/vendor/apexcharts/apexcharts.css" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ url('') }}/assets/vendor/simplebar/simplebar.css" rel="stylesheet" type="text/css">

    <!-- slick css -->
    <link href="{{ url('') }}/assets/vendor/slick/slick.css" rel="stylesheet">
    <link href="{{ url('') }}/assets/vendor/slick/slick-theme.css" rel="stylesheet">

    <!-- filepond css -->
    <link href="{{ url('') }}/assets/vendor/filepond/filepond.css" rel="stylesheet">
    <link href="{{ url('') }}/assets/vendor/filepond/image-preview.min.css" rel="stylesheet">

    <!-- App css-->
    <link href="{{ url('') }}/assets/css/style.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ url('') }}/assets/css/responsive.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="app-wrapper">
        <div class="loader-wrapper">
            <div class="app-loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>


        <!-- Menu Navigation starts -->
        <nav>
            <div class="app-logo">
                <a class="logo d-inline-block" href="index-2.html">
                    <img alt="#" src="{{ url('') }}/assets/logo.png">
                </a>

                <span class="bg-light-primary toggle-semi-nav">
                    <i class="ti ti-chevrons-right f-s-20"></i>
                </span>
            </div>
            <div class="app-nav" id="app-simple-bar">
                <ul class="main-nav p-0 mt-2">
                    <li class="menu-title">
                        <span>Dashboard</span>
                    </li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#dashboard">
                            <i class="iconoir-home-alt"></i>
                            dashboard
                            <span class="badge text-primary-dark bg-primary-300  badge-notification ms-2">4</span>
                        </a>
                        <ul class="collapse" id="dashboard">
                            <li><a href="index-2.html">Project</a></li>
                            <li><a href="ecommerce_dashboard.html">Ecommerce</a></li>
                        </ul>
                    </li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#apps">
                            <i class="iconoir-apple-shortcuts"></i>
                            Apps
                        </a>
                        <ul class="collapse" id="apps">
                            <li><a href="calendar.html">Calender</a></li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#Profile-page">
                                    Profile
                                </a>
                                <ul class="collapse" id="Profile-page">
                                    <li><a href="profile.html">Profile</a></li>
                                    <li><a href="setting.html">Setting</a></li>
                                </ul>
                            </li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse"
                                    href="#projects-page">
                                    Projects Page
                                </a>
                                <ul class="collapse" id="projects-page">
                                    <li><a href="project_app.html">projects</a></li>
                                    <li><a href="project_details.html">projects Details</a></li>
                                </ul>
                            </li>
                            <li><a href="to_do.html">To-Do</a></li>
                            <li><a href="team.html">Team</a></li>
                            <li><a href="api.html">API</a></li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse"
                                    href="#ticket-page">
                                    Ticket
                                </a>
                                <ul class="collapse" id="ticket-page">
                                    <li><a href="ticket.html">Ticket</a></li>
                                    <li><a href="ticket_details.html">Ticket Details</a></li>
                                </ul>
                            </li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse"
                                    href="#email-page">
                                    Email Page
                                </a>
                                <ul class="collapse" id="email-page">
                                    <li><a href="email.html"> Email</a></li>
                                    <li><a href="read_email.html">Read Email</a></li>
                                </ul>
                            </li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#e-shop">
                                    E-shop
                                </a>
                                <ul class="collapse" id="e-shop">
                                    <li><a href="cart.html">Cart</a></li>
                                    <li><a href="product.html">Product</a></li>
                                    <li><a href="add_product.html">Add Product</a></li>
                                    <li><a href="product_details.html">Product-Details</a></li>
                                    <li><a href="product_list.html">Product list</a></li>
                                    <li><a href="orders.html">Orders</a></li>
                                    <li><a href="orders_details.html">Orders Details</a></li>
                                    <li><a href="orders_list.html">Orders List</a></li>
                                    <li><a href="checkout.html">Check out</a></li>
                                    <li><a href="wishlist.html">Wishlist</a></li>
                                </ul>
                            </li>
                            <li><a href="invoice.html">Invoice</a></li>
                            <li><a href="chat.html">Chat</a></li>
                            <li><a href="filemanager.html">File manager</a></li>
                            <li><a href="bookmark.html">Bookmark</a></li>
                            <li><a href="kanban_board.html">Kanban board</a></li>
                            <li><a href="timeline.html">Timeline</a></li>
                            <li><a href="faq.html">FAQS</a></li>
                            <li><a href="pricing.html">Pricing</a></li>
                            <li><a href="gallery.html">Gallery</a></li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#blog-page">
                                    Blog Page
                                </a>
                                <ul class="collapse" id="blog-page">
                                    <li><a href="blog.html">Blog</a></li>
                                    <li><a href="blog_read_more.html">Blog Details</a></li>
                                    <li><a href="add_blog.html">Add Blog</a></li>

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="no-sub">
                        <a class="" href="widget.html">
                            <i class="iconoir-view-grid"></i> Widgets
                        </a>
                    </li>

                    <li class="menu-title"><span>Component</span></li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#ui-kits">

                            <i class="iconoir-handbag"></i>
                            UI kits
                        </a>
                        <ul class="collapse" id="ui-kits">
                            <li><a href="cheatsheet.html">Cheatsheet</a></li>
                            <li><a href="alert.html">Alert</a></li>
                            <li><a href="badges.html">Badges</a></li>
                            <li><a href="buttons.html">Buttons</a></li>
                            <li><a href="cards.html">Cards</a></li>
                            <li><a href="dropdown.html">Dropdown</a></li>
                            <li><a href="grid.html">Grid</a></li>
                            <li><a href="avtar.html">Avtar</a></li>
                            <li><a href="tabs.html">Tabs</a></li>
                            <li><a href="accordions.html">Accordions</a></li>
                            <li><a href="progress.html">Progress</a></li>
                            <li><a href="notifications.html">Notifications</a></li>
                            <li><a href="list.html">Lists</a></li>
                            <li><a href="helper_classes.html">Helper Classes</a></li>
                            <li><a href="background.html">Background</a></li>
                            <li><a href="divider.html">Divider</a></li>
                            <li><a href="ribbons.html">Ribbons</a></li>
                            <li><a href="editor.html">Editor </a></li>
                            <li><a href="collapse.html">Collapse</a></li>
                            <li><a href="footer-page.html">Footer</a></li>
                            <li><a href="shadow.html">Shadow</a></li>
                            <li><a href="wrapper.html">Wrapper</a></li>
                            <li><a href="bullet.html">Bullet</a></li>
                            <li><a href="placeholder.html">Placeholder</a></li>
                            <li><a href="alignment.html">Alignment Thing</a></li>
                        </ul>
                    </li>


                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#advance-ui">
                            <i class="iconoir-shopping-bag-plus"></i> Advance UI
                            <span class="badge text-warning-dark bg-warning-400 badge-notification ms-2">
                                12+
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </a>
                        <ul class="collapse" id="advance-ui">
                            <li><a href="modals.html">Modals</a></li>
                            <li><a href="offcanvas.html">Offcanvas Toggle</a></li>
                            <li><a href="sweetalert.html">Sweat Alert</a></li>
                            <li><a href="scrollbar.html">Scrollbar</a></li>
                            <li><a href="spinners.html">Spinners</a></li>
                            <li><a href="animation.html">Animation</a></li>
                            <li><a href="video_emaded.html">Video Embed</a></li>
                            <li><a href="tour.html">Tour</a></li>
                            <li><a href="slick.html">Slider</a></li>
                            <li><a href="bootstrap_slider.html">Bootstrap Slider</a></li>
                            <li><a href="scrollpy.html">Scrollpy</a></li>
                            <li><a href="tooltips_popovers.html">Tooltip & Popovers</a></li>
                            <li><a href="ratings.html">Rating</a></li>
                            <li><a href="prismjs.html">Prismjs</a></li>
                            <li><a href="count_down.html">Count Down</a></li>
                            <li><a href="count_up.html"> Count up </a></li>
                            <li><a href="draggable.html">Draggable</a></li>
                            <li><a href="tree-view.html">Tree View</a></li>
                            <li><a href="block_ui.html">Block Ui </a></li>
                        </ul>
                    </li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#icons">
                            <i class="iconoir-component"></i> Icons
                        </a>
                        <ul class="collapse" id="icons">
                            <li><a href="fontawesome.html">Fontawesome</a></li>
                            <li><a href="flag_icons.html">Flag</a></li>
                            <li><a href="tabler_icons.html">Tabler</a></li>
                            <li><a href="weather_icon.html">Weather</a></li>
                            <li><a href="animated_icon.html">Animated</a></li>
                            <li><a href="iconoir_icon.html">Iconoir</a></li>
                            <li><a href="phosphor.html">Phosphor</a></li>
                        </ul>
                    </li>
                    <li class="no-sub">
                        <a class="" href="misc.html">
                            <i class="iconoir-bookmark-book"></i> Misc
                        </a>
                    </li>
                    <li class="menu-title"><span>Map & Charts </span></li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#maps">
                            <i class="iconoir-map"></i> Map
                        </a>
                        <ul class="collapse" id="maps">
                            <li><a href="google-map.html">Google Maps</a></li>
                            <li><a href="leaflet-map.html">Leaflet map</a></li>
                        </ul>
                    </li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#chart">
                            <i class="iconoir-lifebelt"></i> Chart
                        </a>
                        <ul class="collapse" id="chart">


                            <li><a href="chart_js.html">Chart js</a></li>


                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse"
                                    href="#apexcharts-page">
                                    Apexcharts
                                </a>
                                <ul class="collapse" id="apexcharts-page">
                                    <li><a href="line.html">Line</a></li>
                                    <li><a href="area.html">Area</a></li>
                                    <li><a href="column.html">Column</a></li>
                                    <li><a href="bar.html">Bar</a></li>
                                    <li><a href="mixed.html">Mixed</a></li>
                                    <li><a href="timeline_range_charts.html">Timeline & Range-Bars</a></li>
                                    <li><a href="candlestick_charts.html">Candlestick</a></li>
                                    <li><a href="boxplot.html">Boxplot</a></li>
                                    <li><a href="bubble.html">Bubble</a></li>
                                    <li><a href="scatter.html">Scatter</a></li>
                                    <li><a href="heatmap.html">Heatmap</a></li>
                                    <li><a href="treemap.html">Treemap</a></li>
                                    <li><a href="pie.html">Pie</a></li>
                                    <li><a href="radialbar.html">Radial bar</a></li>
                                    <li><a href="radar.html">Radar</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-title"><span>Table & forms </span></li>

                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#table">
                            <i class="iconoir-layout-right"></i> Table
                        </a>
                        <ul class="collapse" id="table">
                            <li><a href="basic_table.html">BasicTable</a></li>
                            <li><a href="data_table.html">Data Table</a></li>
                            <li><a href="list_table.html">List Js</a></li>
                            <li><a href="advance_table.html">Advance Table</a></li>
                        </ul>
                    </li>


                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#forms">
                            <i class="iconoir-archive"></i> Forms elements
                        </a>
                        <ul class="collapse" id="forms">
                            <li><a href="form_validation.html">Form Validation</a></li>
                            <li><a href="base_inputs.html">Base Input</a></li>
                            <li><a href="checkbox_radio.html">Checkbox & Radio</a></li>
                            <li><a href="input_groups.html">Input Groups</a></li>
                            <li><a href="input_masks.html">Input Masks</a></li>
                            <li><a href="floating_labels.html">Floating Labels</a></li>
                            <li><a href="date_picker.html">Datetimepicker</a></li>
                            <li><a href="touchspin.html">Touchspin</a></li>
                            <li><a href="select.html">Select2</a></li>
                            <li><a href="switch.html">Switch</a></li>
                            <li><a href="range_slider.html">Range Slider</a></li>
                            <li><a href="typeahead.html">Typeahead</a></li>
                            <li><a href="textarea.html">Textarea</a></li>
                            <li><a href="clipboard.html">Clipboard</a></li>
                            <li><a href="file_upload.html">File Upload</a></li>
                            <li><a href="dual_listboxes.html">Dual Listbox</a></li>
                            <li><a href="default_forms.html">Default Forms</a></li>
                        </ul>
                    </li>

                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#ready_to_use">
                            <i class="iconoir-report-columns"></i>
                            Ready to use <span
                                class="badge text-success-dark bg-success-400 badge-notification ms-2">New</span>
                        </a>
                        <ul class="collapse" id="ready_to_use">
                            <li><a href="form_wizards.html">Form wizards</a></li>
                            <li><a href="form_wizard_1.html">Form wizards 1</a></li>
                            <li><a href="form_wizard_2.html">Form wizards 2</a></li>
                            <li><a href="ready_to_use_form.html">Ready To Use Form</a></li>
                            <li><a href="ready_to_use_table.html">Ready To Use Tables</a></li>
                        </ul>
                    </li>

                    <li class="menu-title"><span>Pages</span></li>

                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#auth_pages">
                            <i class="ph-duotone  ph-notebook"></i> Auth Pages
                        </a>
                        <ul class="collapse" id="auth_pages">
                            <li><a href="sign_in.html">Sign In</a></li>
                            <li><a href="sign_in_1.html">Sign In with Bg-image</a></li>
                            <li><a href="sign_up.html">Sign Up</a></li>
                            <li><a href="sign_up_1.html">Sign Up with Bg-image</a></li>
                            <li><a href="password_reset.html">Password Reset</a></li>
                            <li><a href="password_reset_1.html">Password Reset with Bg-image</a></li>
                            <li><a href="password_create.html">Password Create</a></li>
                            <li><a href="password_create_1.html">Password Create with Bg-image</a></li>
                            <li><a href="lock_screen.html">Lock Screen</a></li>
                            <li><a href="lock_screen_1.html">Lock Screen with Bg-image</a></li>
                            <li><a href="two_step_verification.html">Two-Step Verification</a></li>
                            <li><a href="two_step_verification_1.html">Two-Step Verification with Bg-image</a></li>
                        </ul>
                    </li>
                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#error_pages">
                            <i class="iconoir-warning-triangle"></i> Error Pages
                        </a>
                        <ul class="collapse" id="error_pages">
                            <li><a href="error_400.html">Bad Request </a></li>
                            <li><a href="error_403.html">Forbidden </a></li>
                            <li><a href="error_404.html">Not Found</a></li>
                            <li><a href="error_500.html">Internal Server</a></li>
                            <li><a href="error_503.html">Service Unavailable</a></li>
                        </ul>
                    </li>

                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#other_pages">
                            <i class="iconoir-multiple-pages"></i> Other Pages
                        </a>
                        <ul class="collapse" id="other_pages">
                            <li><a href="blank.html">Blank</a></li>
                            <li><a href="maintenance.html">Maintenance</a></li>
                            <li><a href="landing.html">Landing Page</a></li>
                            <li><a href="coming_soon.html">Coming Soon</a></li>
                            <li><a href="sitemap.html">Sitemap</a></li>
                            <li><a href="privacy_policy.html">Privacy Policy</a></li>
                            <li><a href="terms_condition.html">Terms &amp; Condition</a></li>
                        </ul>
                    </li>

                    <li class="menu-title"><span>Others</span></li>

                    <li>
                        <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#level">
                            <i class="iconoir-keyframes-couple"></i> 2 level
                        </a>
                        <ul class="collapse" id="level">
                            <li><a href="#">Blank</a></li>
                            <li class="another-level">
                                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#level2">
                                    Another level
                                </a>
                                <ul class="collapse" id="level2">
                                    <li><a href="blank.html">Blank</a></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                    <li class="no-sub">
                        <a class=""
                            href="https://phpstack-1384472-5121645.cloudwaysapps.com/document/html/axelit/index.html">
                            <i class="iconoir-page-star"></i> Document
                        </a>
                    </li>

                    <li class="no-sub">
                        <a class="" href="mailto:teqlathemes@gmail.com">
                            <i class="iconoir-chat-bubble-question"></i> Support
                        </a>
                    </li>


                </ul>
            </div>

            <div class="menu-navs">
                <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
                <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
            </div>

        </nav>
        <!-- Menu Navigation ends -->


        <div class="app-content ">
            <div class="">

                <!-- Header Section starts -->
                <header class="header-main">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-6 col-sm-4 d-flex align-items-center header-left p-0">
                                <span class="header-toggle me-3">
                                    <i class="iconoir-view-grid"></i>
                                </span>
                            </div>

                            <div class="col-6 col-sm-8 d-flex align-items-center justify-content-end header-right p-0">

                                <ul class="d-flex align-items-center">

                                    <li class="header-cloud">
                                        <a aria-controls="cloudoffcanvasTops" class="head-icon"
                                            data-bs-target="#cloudoffcanvasTops" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="iconoir-dew-point text-primary f-s-26 me-1"></i>
                                            <span class="f-w-600">26 <sup class="f-s-10">°C</sup></span>
                                        </a>

                                        <div aria-labelledby="cloudoffcanvasTops"
                                            class="offcanvas offcanvas-end header-cloud-canvas"
                                            id="cloudoffcanvasTops" tabindex="-1">
                                            <div class="offcanvas-body p-0">
                                                <div class="cloud-body">

                                                    <div class="cloud-content-box">
                                                        <div class="cloud-box bg-primary-900">
                                                            <p class="mb-3">Mon</p>
                                                            <h6 class="mt-4 f-s-13"> +29°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-cloud-fog text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 2%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-800">
                                                            <p class="mb-3">Tue</p>
                                                            <h6 class="mt-4 f-s-13"> +29°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-cloud-sun text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 2%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-700">
                                                            <p class="mb-3 text-light">Wed</p>
                                                            <h6 class="mt-4 f-s-13"> +20°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 1%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-600">
                                                            <p class="mb-3">Thu</p>
                                                            <h6 class="mt-4 f-s-13"> +17°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 1%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-500">
                                                            <p class="mb-3">Fri</p>
                                                            <h6 class="mt-4 f-s-13"> +18°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 1%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-400">
                                                            <p class="mb-3">Sat</p>
                                                            <h6 class="mt-4 f-s-13"> +16°C</h6>
                                                            <span>
                                                                <i
                                                                    class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 1%
                                                            </p>
                                                        </div>
                                                        <div class="cloud-box bg-primary-300">
                                                            <p class="mb-3">Sun</p>
                                                            <h6 class="mt-4 f-s-13"> +29°C</h6>
                                                            <span class="mb-3">
                                                                <i
                                                                    class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                                            </span>
                                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> 1%
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="header-language">
                                        <div class="flex-shrink-0 dropdown" id="lang_selector">
                                            <a aria-expanded="false" class="d-block head-icon ps-0"
                                                data-bs-toggle="dropdown" href="#">
                                                <div class="lang-flag lang-en ">
                                                    <span class="flag rounded-circle overflow-hidden">
                                                        <i class=""></i>
                                                    </span>
                                                </div>
                                            </a>
                                            <ul class="dropdown-menu language-dropdown header-card border-0">
                                                <li class="lang lang-en selected dropdown-item p-2"
                                                    data-bs-placement="top" data-bs-toggle="tooltip" title="US">
                                                    <span class="d-flex align-items-center">
                                                        <i
                                                            class="flag-icon flag-icon-usa flag-icon-squared b-r-10 f-s-22"></i>
                                                        <span class="ps-2">English</span>
                                                    </span>
                                                </li>
                                                <li class="lang lang-pt dropdown-item p-2" data-bs-title="tooltip"
                                                    data-bs-placement="top" title="FR">
                                                    <span class="d-flex align-items-center">
                                                        <i
                                                            class="flag-icon flag-icon-fra flag-icon-squared b-r-10 f-s-22"></i>
                                                        <span class="ps-2">Française </span>
                                                    </span>
                                                </li>
                                                <li class="lang lang-es dropdown-item p-2" data-bs-title="tooltip"
                                                    data-bs-placement="top" title="UK">
                                                    <span class="d-flex align-items-center">
                                                        <i
                                                            class="flag-icon flag-icon-gbr flag-icon-squared b-r-10 f-s-22"></i>
                                                        <span class="ps-2">English</span>
                                                    </span>
                                                </li>
                                                <li class="lang lang-es dropdown-item p-2" data-bs-title="tooltip"
                                                    data-bs-placement="top" title="Ru">
                                                    <span class="d-flex align-items-center">
                                                        <i
                                                            class="flag-icon flag-icon-rus flag-icon-squared b-r-10 f-s-22"></i>
                                                        <span class="ps-2">Русская</span>
                                                    </span>
                                                </li>
                                                <li class="lang lang-es dropdown-item p-2" data-bs-title="tooltip"
                                                    data-bs-placement="top" title="IT">
                                                    <span class="d-flex align-items-center">
                                                        <i
                                                            class="flag-icon flag-icon-ita flag-icon-squared b-r-10 f-s-22"></i>
                                                        <span class="ps-2">française</span>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>

                                    </li>

                                    <li class="header-searchbar">
                                        <a aria-controls="offcanvasRight" class="d-block head-icon"
                                            data-bs-target="#offcanvasRight" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="iconoir-search"></i>
                                        </a>

                                        <div aria-labelledby="offcanvasRight"
                                            class="offcanvas offcanvas-end header-searchbar-canvas"
                                            id="offcanvasRight" tabindex="-1">
                                            <div class="header-searchbar-header">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <form action="#" class="app-form app-icon-form w-100">
                                                        <div class="position-relative">
                                                            <input aria-label="Search"
                                                                class="form-control search-filter"
                                                                placeholder="Search..." type="search">
                                                            <i class="ti ti-search text-dark"></i>
                                                        </div>
                                                    </form>

                                                    <div class="app-dropdown flex-shrink-0">
                                                        <a aria-expanded="false"
                                                            class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-secondary search-list-avtar ms-2"
                                                            data-bs-auto-close="outside" data-bs-toggle="dropdown"
                                                            href="#" role="button">
                                                            <i class="ph-duotone  ph-gear f-s-20"></i>
                                                        </a>

                                                        <ul class="dropdown-menu mb-3">
                                                            <li class="dropdown-item mt-2">
                                                                <a href="#">
                                                                    <h6 class="mb-0">Search Settings</h6>
                                                                </a>
                                                            </li>
                                                            <li
                                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                                <a href="#">
                                                                    <h6 class="mb-0 text-secondary f-s-14">Safe Search
                                                                        Filtering</h6>
                                                                </a>
                                                                <div class="flex-shrink-0">
                                                                    <div class="form-check form-switch">
                                                                        <input checked
                                                                            class="form-check-input form-check-primary"
                                                                            id="searchSwitch" type="checkbox">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                                <a href="#">
                                                                    <h6 class="mb-0 text-secondary f-s-14">Search
                                                                        Suggestions</h6>
                                                                </a>
                                                                <div class="flex-shrink-0">
                                                                    <div class="form-check form-switch">
                                                                        <input
                                                                            class="form-check-input form-check-primary"
                                                                            id="searchSwitch1" type="checkbox">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-0 text-secondary f-s-14"> Search History
                                                                </h6>
                                                                <i class="ti ti-message-circle me-3  text-success"></i>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li
                                                                class="dropdown-item d-flex align-items-center justify-content-between mb-2">
                                                                <a href="#">
                                                                    <h6 class="mb-0 text-dark f-s-14">Custom Search
                                                                        Preferences</h6>
                                                                </a>
                                                                <div class="flex-shrink-0">
                                                                    <div class="form-check form-switch">
                                                                        <input
                                                                            class="form-check-input form-check-primary"
                                                                            id="searchSwitch2" type="checkbox">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-secondary f-s-15 mt-2">Recently Searched Data:</p>
                                            </div>
                                            <div class="offcanvas-body app-scroll p-0">
                                                <div>
                                                    <ul class="search-list">
                                                        <li class="search-list-item">
                                                            <div
                                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-primary search-list-avtar">
                                                                <i class="ph-duotone  ph-gear f-s-20"></i>
                                                            </div>
                                                            <div class="search-list-content">
                                                                <a href="api.html" target="_blank">
                                                                    <h6 class="mb-0 text-dark">user management</h6>
                                                                </a>
                                                                <p class="f-s-13 mb-0 text-secondary">#RA789</p>
                                                            </div>
                                                        </li>
                                                        <li class="search-list-item">
                                                            <div
                                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-warning search-list-avtar">
                                                                <i
                                                                    class="ph-duotone  ph-projector-screen-chart f-s-20"></i>
                                                            </div>
                                                            <div class="search-list-content">
                                                                <a href="privacy_policy.html" target="_blank">
                                                                    <h6 class="mb-0 text-dark">data visualization</h6>
                                                                </a>
                                                                <p class="f-s-13 mb-0 text-secondary">#RY810</p>
                                                            </div>
                                                        </li>
                                                        <li class="search-list-item">
                                                            <div
                                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-danger search-list-avtar">
                                                                <i class="ph-duotone  ph-shield-check f-s-20"></i>
                                                            </div>
                                                            <div class="search-list-content">
                                                                <a href="terms_condition.html" target="_blank">
                                                                    <h6 class="mb-0 text-dark">security protocols</h6>
                                                                </a>
                                                                <p class="f-s-13 mb-0 text-secondary">#ATR56</p>
                                                            </div>
                                                        </li>
                                                        <li class="search-list-item">
                                                            <div
                                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-info search-list-avtar">
                                                                <i class="ph-duotone  ph-app-window f-s-20"></i>
                                                            </div>
                                                            <div class="search-list-content">
                                                                <a href="sign_in.html" target="_blank">
                                                                    <h6 class="mb-0 text-dark">authentication methods
                                                                    </h6>
                                                                </a>
                                                                <p class="f-s-13 mb-0 text-secondary">#YE615</p>
                                                            </div>
                                                        </li>
                                                        <li class="search-list-item">
                                                            <div
                                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-dark search-list-avtar">
                                                                <i class="ph-duotone  ph-table f-s-20"></i>
                                                            </div>
                                                            <div class="search-list-content">
                                                                <a href="data_table.html" target="_blank">
                                                                    <h6 class="mb-0 text-dark">Data Table</h6>
                                                                </a>
                                                                <p class="f-s-13 mb-0 text-secondary">#YE615</p>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="header-apps">
                                        <a aria-controls="appscanvasRights" class="d-block head-icon"
                                            data-bs-target="#appscanvasRights" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="iconoir-key-command"></i>
                                        </a>

                                        <div aria-labelledby="appscanvasRightsLabel"
                                            class="offcanvas offcanvas-end header-apps-canvas" id="appscanvasRights"
                                            tabindex="-1">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="appscanvasRightsLabel">Shortcut</h5>
                                                <div class="app-dropdown flex-shrink-0">
                                                    <a aria-expanded="false" class=" p-1"
                                                        data-bs-auto-close="outside" data-bs-toggle="dropdown"
                                                        href="#" role="button">
                                                        <i class="ph-bold  ph-faders-horizontal f-s-20"></i>
                                                    </a>
                                                    <ul class="dropdown-menu mb-3 p-2">
                                                        <li class="dropdown-item">
                                                            <a href="setting.html" target="_blank">
                                                                Privacy Settings
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="setting.html" target="_blank">
                                                                Account Settings
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="setting.html" target="_blank">
                                                                Accessibility
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li class="dropdown-item border-0">
                                                            <a aria-expanded="false" data-bs-toggle="dropdown"
                                                                href="#" role="button">
                                                                More Settings
                                                            </a>
                                                            <ul class="dropdown-menu sub-menu">
                                                                <li class="dropdown-item">
                                                                    <a href="setting.html" target="_blank">
                                                                        Backup and Restore
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-item">
                                                                    <a href="setting.html" target="_blank">
                                                                        <span>Data Usage</span>
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-item">
                                                                    <a href="setting.html" target="_blank">
                                                                        <span>Theme</span>
                                                                    </a>
                                                                </li>
                                                                <li
                                                                    class="dropdown-item d-flex align-items-center justify-content-between">
                                                                    <a href="setting.html" target="_blank">
                                                                        <p class="mb-0">Notification</p>
                                                                    </a>
                                                                    <div class="flex-shrink-0">
                                                                        <div class="form-check form-switch">
                                                                            <input
                                                                                class="form-check-input  form-check-primary"
                                                                                id="notificationSwitch"
                                                                                type="checkbox">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="offcanvas-body app-scroll">
                                                <div class="row row-cols-3">
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="product.html" target="_blank">
                                                            <span
                                                                class="text-light-info h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone  ph-shopping-bag-open f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-info">E-shop</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="email.html" target="_blank">
                                                            <span
                                                                class="text-light-primary h-45 w-45 d-flex-center b-r-15 position-relative">
                                                                <i class="ph-duotone  ph-envelope f-s-30"></i>
                                                                <span
                                                                    class="position-absolute top-space-5 start-100 translate-middle p-1 bg-primary-dark rounded-circle animate__animated animate__fadeIn animate__infinite animate__fast"></span>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-primary">Email</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="chat.html" target="_blank">
                                                            <span
                                                                class="text-light-danger h-45 w-45 d-flex-center b-r-15 position-relative">
                                                                <i class="ph-duotone  ph-chat-circle-text f-s-30"></i>
                                                                <span
                                                                    class="position-absolute top-space-5 start-100 translate-middle badge rounded-pill bg-success badge-notification">
                                                                    99+
                                                                    <span class="visually-hidden">unread
                                                                        messages</span>
                                                                </span>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-danger">Chat</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="project_app.html" target="_blank">
                                                            <span
                                                                class="text-light-warning h-45 w-45 d-flex-center b-r-15">
                                                                <i
                                                                    class="ph-duotone ph-projector-screen-chart f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-warning">Project</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="invoice.html" target="_blank">
                                                            <span
                                                                class="text-light-secondary h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone ph-scroll f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-secondary">Invoice</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="blog.html" target="_blank">
                                                            <span
                                                                class="text-light-primary h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone ph-notebook f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-primary">Blog</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="profile.html" target="_blank">
                                                            <span
                                                                class="text-light-primary h-45 w-45 d-flex-center b-r-15 position-relative">
                                                                <i class="ph-duotone ph-users-three f-s-30"></i>
                                                                <span
                                                                    class="position-absolute top-space-5 start-100 translate-middle badge rounded-pill bg-danger badge-notification">
                                                                    <i class="ti ti-bell-ringing"></i>
                                                                </span>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-primary">Profile</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="gallery.html" target="_blank">
                                                            <span
                                                                class="text-light-success h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone ph-google-photos-logo f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-success">Gallery</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="kanban_board.html" target="_blank">
                                                            <span
                                                                class="text-light-info h-45 w-45 d-flex-center b-r-15">
                                                                <i
                                                                    class="ph-duotone ph-selection-foreground text-info f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-secondary">Task </p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="calendar.html" target="_blank">
                                                            <span
                                                                class="text-light-dark h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone ph-calendar f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-dark">Calen..</p>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex-center text-center mb-3">
                                                        <a href="filemanager.html" target="_blank">
                                                            <span
                                                                class="text-light-danger h-45 w-45 d-flex-center b-r-15">
                                                                <i class="ph-duotone ph-folder-open f-s-30"></i>
                                                            </span>
                                                            <p class="mb-0 f-w-500 text-danger">File Ma..</p>
                                                        </a>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="header-cart">
                                        <a aria-controls="cartcanvasRight" class="d-block head-icon position-relative"
                                            data-bs-target="#cartcanvasRight" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="iconoir-shopping-bag"></i>
                                            <span
                                                class="position-absolute translate-middle badge rounded-pill bg-danger badge-notification">4</span>
                                        </a>
                                        <div aria-labelledby="cartcanvasRightLabel"
                                            class="offcanvas offcanvas-end header-cart-canvas" id="cartcanvasRight"
                                            tabindex="-1">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="cartcanvasRightLabel">Cart</h5>
                                                <button aria-label="Close" class="btn-close"
                                                    data-bs-dismiss="offcanvas" type="button"></button>
                                            </div>
                                            <div class="offcanvas-body app-scroll p-0">
                                                <div class="head-container">
                                                    <div class="head-box">
                                                        <img alt="cart" class="h-50 me-3 b-r-10"
                                                            src="{{ url('') }}/assets/images/ecommerce/19.jpg">
                                                        <div class="flex-grow-1">
                                                            <a class="text-primary-dark mb-0 f-w-600 f-s-16"
                                                                href="product_details.html" target="_blank">
                                                                Backpacks <span class="text-warning-dark"> (3<i
                                                                        class="ti ti-star-filled text-warning f-s-12"></i>)</span>
                                                            </a>
                                                            <div>
                                                                <span class="text-secondary"><span
                                                                        class="text-dark f-w-500">size</span> :
                                                                    medium</span>
                                                                <span class="text-secondary ms-2"><span
                                                                        class="text-dark f-w-500">Color</span>
                                                                    :Pink</span>
                                                            </div>

                                                        </div>
                                                        <div class="text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                            <p class="text-success f-w-500 mb-0">$600.50 x 1</p>
                                                        </div>
                                                    </div>
                                                    <div class="head-box">
                                                        <img alt="cart" class="h-50 object-fit-cover me-3 b-r-10"
                                                            src="{{ url('') }}/assets/images/ecommerce/13.jpg">
                                                        <div class="flex-grow-1">
                                                            <a class="text-info-dark mb-0 f-w-600 f-s-16"
                                                                href="product_details.html" target="_blank">
                                                                Women's Watch<span class="text-warning-dark"> (4<i
                                                                        class="ti ti-star-filled text-warning f-s-12"></i>)</span>
                                                            </a>
                                                            <div>
                                                                <span class="text-secondary"><span
                                                                        class="text-dark f-w-500">size</span> :
                                                                    small</span>
                                                                <span class="text-secondary ms-2"><span
                                                                        class="text-dark f-w-500">Color</span> :
                                                                    RoseGold</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                            <p class="text-success f-w-500 mb-0">$519.10 x 2</p>
                                                        </div>
                                                    </div>
                                                    <div class="head-box">
                                                        <img alt="cart" class="h-50 object-fit-cover me-3 b-r-10"
                                                            src="{{ url('') }}/assets/images/ecommerce/09.jpg">
                                                        <div class="flex-grow-1">
                                                            <a class="text-danger-dark mb-0 f-w-600 f-s-16"
                                                                href="product_details.html"
                                                                target="_blank">Sandals<span
                                                                    class="text-warning-dark"> (5<i
                                                                        class="ti ti-star-filled text-warning f-s-12"></i>)</span>
                                                            </a>
                                                            <div>
                                                                <span class="text-secondary"><span
                                                                        class="text-dark f-w-500">size</span> :
                                                                    8</span>
                                                                <span class="text-secondary ms-2"><span
                                                                        class="text-dark f-w-500">Color</span>
                                                                    :White</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                            <p class="text-success f-w-500 mb-0">$390 x 2</p>
                                                        </div>
                                                    </div>
                                                    <div class="head-box ">
                                                        <img alt="cart" class="h-50 object-fit-cover me-3 b-r-10"
                                                            src="{{ url('') }}/assets/images/ecommerce/23.jpg">
                                                        <div class="flex-grow-1">
                                                            <a class="text-success-dark mb-0 f-w-600 f-s-16"
                                                                href="product_details.html" target="_blank">
                                                                Jackets<span class="text-warning-dark"> (3<i
                                                                        class="ti ti-star-filled text-warning f-s-12"></i>)</span>
                                                            </a>
                                                            <div>
                                                                <span class="text-secondary"><span
                                                                        class="text-dark f-w-500">size</span> :
                                                                    XL</span>
                                                                <span class="text-secondary ms-2"><span
                                                                        class="text-dark f-w-500">Color</span>
                                                                    :Blue</span>
                                                            </div>

                                                        </div>
                                                        <div class="text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                            <p class="text-success f-w-500 mb-0">$300.00 x 2</p>
                                                        </div>
                                                    </div>
                                                    <div class="head-box ">
                                                        <img alt="cart" class="h-50 object-fit-cover me-3 b-r-10"
                                                            src="{{ url('') }}/assets/images/ecommerce/11.jpg">
                                                        <div class="flex-grow-1">
                                                            <a class="text-dark-dark mb-0 f-w-600 f-s-16"
                                                                href="product_details.html" target="_blank">
                                                                Shoes<span class="text-warning-dark"> (2<i
                                                                        class="ti ti-star-filled text-warning f-s-12"></i>)</span>
                                                            </a>
                                                            <div>
                                                                <span class="text-secondary"><span
                                                                        class="text-dark f-w-500">size</span> :
                                                                    9</span>
                                                                <span class="text-secondary ms-2"><span
                                                                        class="text-dark f-w-500">Color</span>
                                                                    :White</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                            <p class="text-success f-w-500 mb-0">$450.00 x 1</p>
                                                        </div>
                                                    </div>
                                                    <div class="hidden-massage py-4 px-3">
                                                        <img alt="cart" class="img-fluid mb-3"
                                                            src="{{ url('') }}/assets/images/header/cart_empty.gif">
                                                        <div>
                                                            <h6 class="mb-0">Your Cart is Empty</h6>
                                                            <p class="text-secondary mb-0">Add some items :)</p>
                                                            <a class="btn btn-light-primary btn-xs mt-2"
                                                                href="product_details.html">Shop
                                                                Now</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="offcanvas-footer">
                                                <div class="head-box-footer p-3">
                                                    <div class="mb-4">
                                                        <h6 class="text-muted f-w-600">Total <span
                                                                class="float-end">$3,468.00</span>
                                                        </h6>
                                                    </div>
                                                    <div class="header-cart-btn">
                                                        <a class="btn btn-light-primary" href="cart.html"
                                                            role="button" target="_blank">
                                                            <i class="ti ti-eye"></i> View Cart</a>
                                                        <a class="btn btn-light-success" href="checkout.html"
                                                            role="button" target="_blank">
                                                            Checkout <i class="ti ti-shopping-cart"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="header-dark">
                                        <div class="sun-logo head-icon">
                                            <i class="iconoir-sun-light"></i>
                                        </div>
                                        <div class="moon-logo head-icon">
                                            <i class="iconoir-half-moon"></i>
                                        </div>
                                    </li>

                                    <li class="header-notification">
                                        <a aria-controls="notificationcanvasRight"
                                            class="d-block head-icon position-relative"
                                            data-bs-target="#notificationcanvasRight" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="iconoir-bell"></i>
                                            <span
                                                class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle animate__animated animate__fadeIn animate__infinite animate__slower"></span>
                                        </a>
                                        <div aria-labelledby="notificationcanvasRightLabel"
                                            class="offcanvas offcanvas-end header-notification-canvas"
                                            id="notificationcanvasRight" tabindex="-1">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="notificationcanvasRightLabel">
                                                    Notification</h5>
                                                <button aria-label="Close" class="btn-close"
                                                    data-bs-dismiss="offcanvas" type="button"></button>
                                            </div>
                                            <div class="offcanvas-body notification-offcanvas-body app-scroll p-0">
                                                <div class="head-container notification-head-container">
                                                    <div class="notification-message head-box">
                                                        <div class="message-images">
                                                            <span
                                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                                <img alt="avtar" class="img-fluid b-r-10"
                                                                    src="{{ url('') }}/assets/images/ai_avtar/6.jpg">
                                                                <span
                                                                    class="position-absolute bottom-30 end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                                            </span>
                                                        </div>
                                                        <div class="message-content-box flex-grow-1 ps-2">

                                                            <a class="f-s-15 text-secondary mb-0"
                                                                href="read_email.html" target="_blank"><span
                                                                    class="f-w-500 text-secondary">Gene Hart</span>
                                                                wants to
                                                                edit <span
                                                                    class="f-w-500 text-secondary">Report.doc</span></a>
                                                            <div>
                                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                                    href="#">Approve</a>
                                                                <a class="d-inline-block f-w-500 text-danger"
                                                                    href="#">Deny</a>
                                                            </div>
                                                            <span class="badge text-light-primary mt-2"> sep 23 </span>

                                                        </div>
                                                        <div class="align-self-start text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-images">
                                                            <span
                                                                class="bg-light-dark h-35 w-35 d-flex-center b-r-10 position-relative">
                                                                <i class="ph-duotone  ph-truck f-s-18"></i>
                                                            </span>
                                                        </div>
                                                        <div class="message-content-box flex-grow-1 ps-2">
                                                            <a class="f-s-15 text-secondary mb-0"
                                                                href="read_email.html" target="_blank">Hey
                                                                <span class="f-w-500 text-secondary">Emery
                                                                    McKenzie</span>,
                                                                get ready: Your order from <span
                                                                    class="f-w-500 text-secondary">@Shopper.com</span>
                                                                is out for delivery today!</a>
                                                            <span class="badge text-light-info mt-2"> sep 23 </span>

                                                        </div>
                                                        <div class="align-self-start text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-images">
                                                            <span
                                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                                <img alt="" class="img-fluid b-r-10"
                                                                    src="{{ url('') }}/assets/images/ai_avtar/2.jpg">
                                                                <span
                                                                    class="position-absolute  end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                                            </span>
                                                        </div>
                                                        <div class="message-content-box flex-grow-1 ps-2">
                                                            <a class="f-s-15 text-secondary mb-0"
                                                                href="read_email.html" target="_blank"><span
                                                                    class="f-w-500 text-secondary">Simon Young</span>
                                                                shared
                                                                a file called <span
                                                                    class="f-w-500 text-secondary">Dropdown.pdf</span></a>
                                                            <span class="badge text-light-success mt-2"> 30 min</span>

                                                        </div>
                                                        <div class="align-self-start text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-images">
                                                            <span
                                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                                <img alt="" class="img-fluid b-r-10"
                                                                    src="{{ url('') }}/assets/images/ai_avtar/5.jpg">
                                                                <span
                                                                    class="position-absolute end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                                            </span>
                                                        </div>
                                                        <div class="message-content-box flex-grow-1 ps-2">
                                                            <a class="f-s-15 text-secondary mb-0"
                                                                href="read_email.html" target="_blank"><span
                                                                    class="f-w-500 text-secondary">Becky G.
                                                                    Hayes</span> has
                                                                added a comment to <span
                                                                    class="f-w-500 text-secondary">Final_Report.pdf</span></a>
                                                            <span class="badge text-light-warning mt-2"> 45 min</span>
                                                        </div>
                                                        <div class="align-self-start text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-images">
                                                            <span
                                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                                <img alt="" class="img-fluid b-r-10"
                                                                    src="{{ url('') }}/assets/images/ai_avtar/1.jpg">
                                                                <span
                                                                    class="position-absolute  end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                                            </span>
                                                        </div>
                                                        <div class="message-content-box flex-grow-1 ps-2">
                                                            <a class="f-s-15 text-secondary mb-0"
                                                                href="read_email.html" target="_blank"><span
                                                                    class="f-w-600 text-secondary">Romaine
                                                                    Nadeau</span>
                                                                invited you to join a meeting
                                                            </a>
                                                            <div>
                                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                                    href="#">Join</a>
                                                                <a class="d-inline-block f-w-500 text-danger"
                                                                    href="#">Decline</a>
                                                            </div>

                                                            <span class="badge text-light-secondary mt-2"> 1 hour ago
                                                            </span>
                                                        </div>
                                                        <div class="align-self-start text-end">
                                                            <i class="iconoir-xmark close-btn"></i>
                                                        </div>
                                                    </div>

                                                    <div class="hidden-massage py-4 px-3">
                                                        <img alt="" class="w-50 h-50 mb-3 mt-2"
                                                            src="{{ url('') }}/assets/images/icons/bell.png">
                                                        <div>
                                                            <h6 class="mb-0">Notification Not Found</h6>
                                                            <p class="text-secondary">When you have any notifications
                                                                added
                                                                here,will
                                                                appear here.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </li>

                                    <li class="header-profile">
                                        <a aria-controls="profilecanvasRight" class="d-block head-icon"
                                            data-bs-target="#profilecanvasRight" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark"
                                                src="{{ url('') }}/assets/images/avtar/woman.jpg">
                                        </a>

                                        <div aria-labelledby="profilecanvasRight"
                                            class="offcanvas offcanvas-end header-profile-canvas"
                                            id="profilecanvasRight" tabindex="-1">
                                            <div class="offcanvas-body app-scroll">
                                                <ul class="">
                                                    <li class="d-flex gap-3 mb-3">
                                                        <div class="d-flex-center">
                                                            <span
                                                                class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                                                <img alt="" class="img-fluid b-r-10"
                                                                    src="{{ url('') }}/assets/images/avtar/woman.jpg">
                                                            </span>
                                                        </div>
                                                        <div class="text-center mt-2">
                                                            <h6 class="mb-0"> Laura Monaldo <img
                                                                    alt="instagram-check-mark" class="w-20 h-20"
                                                                    src="{{ url('') }}/assets/images/profile-app/01.png">
                                                            </h6>
                                                            <p class="f-s-12 mb-0 text-secondary">
                                                                lauradesign@gmail.com</p>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <a class="f-w-500" href="profile.html" target="_blank">
                                                            <i class="iconoir-user-love pe-1 f-s-20"></i> Profile
                                                            Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="f-w-500" href="setting.html" target="_blank">
                                                            <i class="iconoir-settings pe-1 f-s-20"></i> Settings
                                                        </a>
                                                    </li>
                                                    <li class="app-divider-v dotted py-1"></li>
                                                    <li>
                                                        <a class="f-w-500" href="setting.html" target="_blank">
                                                            <i class="iconoir-eye-closed pe-1 f-s-20"></i> Hide
                                                            Settings
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between">
                                                            <a class="f-w-500" href="#">
                                                                <i class="iconoir-bell-notification pe-1 f-s-20"></i>
                                                                Notification
                                                            </a>
                                                            <div class="flex-shrink-0">
                                                                <div class="form-check form-switch">
                                                                    <input checked
                                                                        class="form-check-input form-check-primary"
                                                                        id="basicSwitch" type="checkbox">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <a class="f-w-500" href="#">
                                                                    <i
                                                                        class="ph-duotone  ph-detective pe-1 f-s-20"></i>
                                                                    Incognito
                                                                </a>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input form-check-primary"
                                                                        id="incognitoSwitch" type="checkbox">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="app-divider-v dotted py-1"></li>
                                                    <li>
                                                        <a class="f-w-500" href="faq.html" target="_blank">
                                                            <i class="iconoir-help-circle pe-1 f-s-20"></i> Help
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="f-w-500" href="pricing.html" target="_blank">
                                                            <i class="iconoir-dollar pe-1 f-s-20"></i>
                                                            Pricing
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="mb-0 text-secondary f-w-500" href="sign_up.html"
                                                            target="_blank">
                                                            <i class="iconoir-plus pe-1 f-s-20"></i> Add account
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="mb-0 btn btn-light-danger btn-sm justify-content-center "
                                                            href="sign_in.html" role="button">
                                                            <i class="ph-duotone  ph-sign-out pe-1 f-s-20"></i> Log
                                                            Out
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>
                <!-- Header Section ends -->

                <!-- Body main section starts -->
                <main>
                    <div class="container-fluid mt-3">
                        <div class="row">

                            <div class="col-lg-6 col-xxl-4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card project-total-card">
                                            <div class="card-body">
                                                <div class="d-flex position-relative">
                                                    <h5 class="text-dark txt-ellipsis-1">Total Hours</h5>
                                                    <div class="clock-box">
                                                        <div class="clock">
                                                            <div class="hour" id="hour"></div>
                                                            <div class="min" id="min"></div>
                                                            <div class="sec" id="sec"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="d-flex">
                                                        <h2 class="text-info-dark hour-display">00H</h2>
                                                    </div>
                                                    <div class="progress-labels mg-t-40">
                                                        <span class="text-info">Productive</span>
                                                        <span class="text-info">Middle</span>
                                                        <span class="text-info">Idle</span>
                                                    </div>
                                                    <div class="custom-progress-container info-progress">
                                                        <div class="progress-bar productive"></div>
                                                        <div class="progress-bar middle"></div>
                                                        <div class="progress-bar idle"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="card bg-info-300 project-details-card">
                                            <div class="card-body">
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-white-300 text-info-dark p-1  b-r-10">📱
                                                        Mobile app</span>
                                                    <span
                                                        class="badge dashed-1-info text-info-dark ms-2 p-1  b-r-10">Marketing</span>
                                                </div>
                                                <div class="my-4">
                                                    <h5 class="f-w-600 text-info-dark txt-ellipsis-1">Project Alpha
                                                    </h5>
                                                    <p class="text-info f-s-13 txt-ellipsis-1 mb-0">Revolutionizing
                                                        ideas,
                                                        empowering innovation, and driving success.</p>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between pt-3">
                                                    <ul class="avatar-group">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-light-primary b-2-primary">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/4.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-light-success b-2-success"
                                                            data-bs-title="Lennon Briggs" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/5.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-light-info b-2-info"
                                                            data-bs-title="Maya Horton" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/6.png">
                                                        </li>
                                                    </ul>
                                                    <span class="badge bg-white-300 text-info-dark ms-2 ">🔥 1H
                                                        left</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="card bg-success-300 project-details-card">
                                            <div class="card-body">
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-white-300 text-success-dark p-1 b-r-10">⚡
                                                        API </span>
                                                    <span
                                                        class="badge bg-transparent dashed-1-dark-light text-success-dark ms-2 p-1 b-r-10">Web
                                                        Development</span>
                                                </div>
                                                <div class="my-4">
                                                    <h5 class="f-w-600 text-success-dark txt-ellipsis-1">Project Beta
                                                    </h5>
                                                    <p class="text-success f-s-13 txt-ellipsis-1 mb-0"> Innovating
                                                        solutions
                                                        for seamless task management efficiency.</p>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between pt-3">
                                                    <ul class="avatar-group">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-light-primary b-2-primary">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/4.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-light-danger b-2-danger"
                                                            data-bs-title="Maya Horton" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/6.png">
                                                        </li>
                                                    </ul>
                                                    <span class="badge bg-white-300 text-success-dark ms-2 ">✨ 2D
                                                        left</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="card core-teams-card">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <h5 class="text-dark f-w-600 txt-ellipsis-1">💼 Core Teams</h5>
                                                </div>
                                                <div>
                                                    <h2
                                                        class="text-warning-dark my-4 d-inline-flex align-items-baseline">
                                                        1k
                                                        <span class="f-s-12 text-dark">Team Members</span>
                                                    </h2>
                                                    <ul class="avatar-group justify-content-start ">
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden text-bg-primary b-2-light"
                                                            data-bs-title="Sabrina Torres" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/4.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden text-bg-success b-2-light"
                                                            data-bs-title="Eva Bailey" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/5.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden text-bg-danger b-2-light"
                                                            data-bs-title="Michael Hughes" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/6.png">
                                                        </li>
                                                        <li class="text-bg-secondary h-35 w-35 d-flex-center b-r-50"
                                                            data-bs-title="10 More" data-bs-toggle="tooltip">
                                                            10+
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xxl-4">
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div>
                                            <input class="file-uploader-box filelight file-light-info"
                                                data-allow-reorder="true" id="fileUploaderBox" multiple="multiple"
                                                type="file">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card ">
                                            <div class="card-body">
                                                <div class="project-expense" id="projectExpense"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6 col-xxl-4 ">
                                <div class="card project-connect-card">
                                    <div class="card-body pb-0">
                                        <div class="text-center">
                                            <h5 class=" mb-2 f-s-24">Get started <span
                                                    class="text-primary f-w-700">Effortlessly.</span>
                                            </h5>
                                            <p class="f-s-14 text-dark pb-0 txt-ellipsis-2">
                                                Connect your team's tools and unlock a unified view of every project's
                                                progress, deadlines, and team contributions.
                                            </p>
                                        </div>
                                        <div class="connect-chat-box">
                                            <div class="avatar-connect-box">
                                                <img alt="logo" class="avatar-connect-logo"
                                                    src="{{ url('') }}/assets/images/dashboard/project/avatar.png">
                                                <img alt="logo" class="dribbble-connect-logo"
                                                    src="{{ url('') }}/assets/images/dashboard/project/dribbble.png">
                                            </div>
                                            <img alt="img"
                                                src="{{ url('') }}/assets/images/dashboard/project/chat.png">
                                            <img alt="logo"
                                                class="slack-logo animate__shakeY animate__animated animate__infinite animate__slower"
                                                src="{{ url('') }}/assets/images/dashboard/project/slack.png">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xxl-3 ">
                                <div class="p-3">
                                    <h5>Tracker</h5>
                                </div>
                                <div class="card">
                                    <div class="card-body position-relative">
                                        <div class="time-tracker">
                                            <div class="share-time">
                                                <div class="dropdown">
                                                    <span aria-expanded="false"
                                                        class="w-35 h-35 bg-primary-300 text-info-dark rounded p-2 d-flex-center"
                                                        data-bs-toggle="dropdown" role="button">
                                                        <i class="iconoir-share-android-solid"></i>
                                                    </span>
                                                    <ul class="dropdown-menu  dropdown-menu-end rounded">
                                                        <li><a class="dropdown-item f-s-14" href="#"><i
                                                                    class="iconoir-instagram text-danger-dark me-2 f-s-18 align-text-top"></i>Instagram</a>
                                                        </li>
                                                        <li><a class="dropdown-item f-s-14" href="#"><i
                                                                    class="iconoir-twitter text-twitter me-2 f-s-18 align-text-top"></i>Twitter</a>
                                                        </li>
                                                        <li><a class="dropdown-item f-s-14" href="#"><i
                                                                    class="iconoir-chat-lines text-whatsapp me-2 f-s-18 align-text-top"></i>Messenger</a>
                                                        </li>
                                                        <li><a class="dropdown-item f-s-14" href="#"><i
                                                                    class="iconoir-more-horiz text-dark me-2 f-s-18 align-text-top"></i>Other
                                                                Apps</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h2 class="timer-display f-w-600">00:00:00</h2>
                                            <div class="controls">
                                                <button class="btn btn-light-primary icon-btn b-r-18"
                                                    id="start-btn"><i class="iconoir-play-solid"></i></button>
                                                <button class="btn btn-danger icon-btn b-r-18" disabled
                                                    id="stop-btn"><i class="iconoir-square"></i></button>
                                                <button class="btn btn-light-info icon-btn b-r-18" id="reset-btn"><i
                                                        class="iconoir-refresh"></i></button>
                                            </div>
                                        </div>

                                        <ul class="tracker-history-list app-scroll mt-3">
                                            <li class="bg-info-300">
                                                <div>
                                                    <h6 class="text-info-dark mb-0">Session 1</h6>
                                                </div>
                                                <div class="text-dark f-w-600 ms-2">
                                                    00:01:23
                                                </div>
                                            </li>

                                            <li class="bg-primary-300">
                                                <div>
                                                    <h6 class="text-primary-dark mb-0">Session 2</h6>
                                                </div>
                                                <div class="text-dark f-w-600 ms-2">
                                                    00:02:45
                                                </div>
                                            </li>
                                            <li class="bg-danger-300">
                                                <div>
                                                    <h6 class="text-danger-dark mb-0">Session 3</h6>
                                                </div>
                                                <div class="text-dark f-w-600 ms-2">
                                                    00:03:30
                                                </div>
                                            </li>
                                            <li class="bg-warning-300">
                                                <div>
                                                    <h6 class="text-warning-dark mb-0">Session 4</h6>
                                                </div>
                                                <div class="text-dark f-w-600 ms-2">
                                                    00:04:12
                                                </div>
                                            </li>
                                            <li class="bg-success-300">
                                                <div>
                                                    <h6 class="text-success-dark mb-0">Session 5</h6>
                                                </div>
                                                <div class="text-dark f-w-600 ms-2">
                                                    01:06:00
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7 col-xxl-6 order-1-md">
                                <div class="p-3">
                                    <h5>Project Status</h5>
                                </div>
                                <div class="card mb-0">
                                    <div class="card-body py-2 px-0 overflow-hidden">
                                        <div class="table-responsive app-scroll ">
                                            <table class="table align-middle project-status-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Project</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">TeamLead</th>
                                                        <th scope="col">priority</th>
                                                        <th scope="col">Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-success-dark text-nowrap">Web
                                                                Redesign</h6>
                                                        </td>
                                                        <td><span class="badge text-light-warning f-s-9 f-w-700">In
                                                                Progress</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto "
                                                                data-bs-title="Athena Stewart"
                                                                data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/2.png">
                                                            </a>


                                                        </td>
                                                        <td class="text-success-dark f-w-600">
                                                            High
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap"><i
                                                                    class="ti ti-circle-filled me-2 f-s-6"></i> Design
                                                                phase completed</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-warning-dark text-nowrap">Mobile App
                                                            </h6>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge text-light-success f-s-9 f-w-700">Completed</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Jane Smith" data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/3.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-secondary-dark f-w-600">
                                                            Medium
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap"><i
                                                                    class="ti ti-circle-filled me-2 f-s-6"></i>
                                                                Project deployed successfully</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-danger-dark text-nowrap">
                                                                Campaign</h6>
                                                        </td>
                                                        <td>
                                                            <span class="badge text-light-secondary f-s-9 f-w-700">Not
                                                                Started</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Mark Lee" data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/4.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-danger-dark f-w-600">
                                                            Low
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap"><i
                                                                    class="ti ti-circle-filled me-2 f-s-6"></i>
                                                                Campaign to begin in December</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-primary-dark text-nowrap">E-Commerce
                                                            </h6>
                                                        </td>
                                                        <td>
                                                            <span class="badge text-light-warning f-s-9 f-w-700">In
                                                                Progress</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Alice Johnson"
                                                                data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/5.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-success-dark f-w-600">
                                                            High
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap"><i
                                                                    class="ti ti-circle-filled me-2 f-s-6"></i>
                                                                Initial setup </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-success-dark text-nowrap">Social
                                                                Media </h6>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge text-light-success f-s-9 f-w-700">Completed</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Sophia Green"
                                                                data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/4.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-danger-dark f-w-600">
                                                            Low
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap">
                                                                <i class="ti ti-circle-filled me-2 f-s-6"></i>
                                                                Campaign launched successfully
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-success-dark text-nowrap">SEO
                                                                Optimization</h6>
                                                        </td>
                                                        <td>
                                                            <span class="badge text-light-warning f-s-9 f-w-700">In
                                                                Progress</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Liam Carter"
                                                                data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/5.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-success-dark f-w-600">
                                                            Medium
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap">
                                                                <i class="ti ti-circle-filled me-2 f-s-6"></i> Keyword
                                                                analysis ongoing
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h6 class="mb-0 text-success-dark text-nowrap">UI/UX
                                                                Revamp</h6>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge text-light-info f-s-9 f-w-700">Scheduled</span>
                                                        </td>
                                                        <td class="f-w-600 text-dark">
                                                            <a class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-secondary m-auto"
                                                                data-bs-title="Olivia Brown"
                                                                data-bs-toggle="tooltip">
                                                                <img alt="avtar" class="img-fluid"
                                                                    src="{{ url('') }}/assets/images/avtar/6.png">
                                                            </a>
                                                        </td>
                                                        <td class="text-danger-dark f-w-600">
                                                            Low
                                                        </td>
                                                        <td>
                                                            <span class="text-dark f-s-14 f-w-500 text-nowrap">
                                                                <i class="ti ti-circle-filled me-2 f-s-6"></i>
                                                                Resources allocated
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-footer ">
                                    <p class="mb-0 f-s-15 f-w-500 txt-ellipsis-1">Showing 7 to 20 of 20 entries</p>
                                    <ul class="pagination app-pagination justify-content-end ">
                                        <li class="page-item disabled"><a class="page-link b-r-left"
                                                href="#">Previous</a></li>
                                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">2</a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-5 col-xxl-3">
                                <div class="p-3">
                                    <h5>Today Tasks</h5>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="task-container slider">
                                            <div class="card task-card bg-danger-300">
                                                <div class="card-body">
                                                    <h6 class="text-danger-dark txt-ellipsis-1">Finalize Project
                                                        Proposal</h6>

                                                    <ul class="avatar-group justify-content-start my-3">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-primary">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/4.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-success"
                                                            data-bs-title="Lennon Briggs" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/5.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-danger"
                                                            data-bs-title="Maya Horton" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/6.png">
                                                        </li>
                                                    </ul>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div aria-valuemax="100" aria-valuemin="0"
                                                            aria-valuenow="25" class="progress w-100"
                                                            role="progressbar">
                                                            <div class="progress-bar bg-danger-dark progress-bar-striped progress-bar-animated"
                                                                style="width: 68%"></div>
                                                        </div>
                                                        <span class="badge bg-white-300 text-danger-dark ms-2 ">+
                                                            68%</span>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="card">
                                                <div
                                                    class="d-flex justify-content-between align-items-center rounded p-1 bg-primary-300">
                                                    <span class="bg-primary h-35 w-35 d-flex-center rounded">
                                                        <i class="iconoir-group f-s-18"></i>
                                                    </span>
                                                    <h6 class="mb-0 txt-ellipsis-1">Meeting</h6>
                                                    <div class="d-flex gap-2">
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-more-horiz f-s-18"></i>
                                                        </span>
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-copy f-s-18"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card task-card bg-warning-300">
                                                <div class="card-body">
                                                    <h6 class="text-warning-dark txt-ellipsis-1">Design Homepage
                                                        Layout</h6>
                                                    <ul class="avatar-group justify-content-start my-3">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-primary">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/3.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-info"
                                                            data-bs-title="Sophia Turner" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/7.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-warning"
                                                            data-bs-title="Lucas Green" data-bs-toggle="tooltip">
                                                            <img alt="avtar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/8.png">
                                                        </li>
                                                    </ul>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div aria-valuemax="100" aria-valuemin="0"
                                                            aria-valuenow="25" class="progress w-100"
                                                            role="progressbar">
                                                            <div class="progress-bar bg-warning-dark progress-bar-striped progress-bar-animated"
                                                                style="width: 35%"></div>
                                                        </div>

                                                        <span class="badge bg-white-400 text-secondary-dark ms-2">+
                                                            35%</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="card">
                                                <div
                                                    class="d-flex justify-content-between align-items-center rounded p-1 bg-info-300">
                                                    <span class="bg-info h-35 w-35 d-flex-center rounded">
                                                        <i class="iconoir-group f-s-18"></i>
                                                    </span>
                                                    <h6 class="mb-0 txt-ellipsis-1">Meeting</h6>
                                                    <div class="d-flex gap-2">
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-more-horiz f-s-18"></i>
                                                        </span>
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-copy f-s-18"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card">
                                                <div
                                                    class="d-flex justify-content-between align-items-center rounded p-1 bg-success-300">
                                                    <span class="bg-success h-35 w-35 d-flex-center rounded">
                                                        <i class="iconoir-group f-s-18"></i>
                                                    </span>
                                                    <h6 class="mb-0 txt-ellipsis-1">Meeting</h6>
                                                    <div class="d-flex gap-2">
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-more-horiz f-s-18"></i>
                                                        </span>
                                                        <span
                                                            class="w-35 h-35 bg-white-300 text-info-dark rounded p-2 d-flex-center"><i
                                                                class="iconoir-copy f-s-18"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card task-card bg-info-300">
                                                <div class="card-body">
                                                    <h6 class="text-info-dark txt-ellipsis-1">Develop API Integration
                                                    </h6>
                                                    <ul class="avatar-group justify-content-start my-3">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-info">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/4.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-info"
                                                            data-bs-title="Michael Johnson"
                                                            data-bs-toggle="tooltip">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/6.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-warning"
                                                            data-bs-title="Emily Brown" data-bs-toggle="tooltip">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/5.png">
                                                        </li>
                                                    </ul>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div aria-valuemax="100" aria-valuemin="0"
                                                            aria-valuenow="60" class="progress w-100"
                                                            role="progressbar">
                                                            <div class="progress-bar bg-info-dark progress-bar-striped progress-bar-animated"
                                                                style="width: 60%"></div>
                                                        </div>
                                                        <span class="badge bg-white-400 text-secondary-dark ms-2">+
                                                            60%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card task-card bg-success-300">
                                                <div class="card-body">
                                                    <h6 class="text-success-dark txt-ellipsis-1">Test User Feedback
                                                    </h6>
                                                    <ul class="avatar-group justify-content-start my-3">
                                                        <li
                                                            class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-primary">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/9.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-info"
                                                            data-bs-title="Alice Smith" data-bs-toggle="tooltip">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/10.png">
                                                        </li>
                                                        <li class="h-35 w-35 d-flex-center b-r-50 overflow-hidden bg-success"
                                                            data-bs-title="John Doe" data-bs-toggle="tooltip">
                                                            <img alt="avatar" class="img-fluid"
                                                                src="{{ url('') }}/assets/images/avtar/11.png">
                                                        </li>
                                                    </ul>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div aria-valuemax="100" aria-valuemin="0"
                                                            aria-valuenow="80" class="progress w-100"
                                                            role="progressbar">
                                                            <div class="progress-bar bg-success-dark progress-bar-striped progress-bar-animated"
                                                                style="width: 80%"></div>
                                                        </div>
                                                        <span class="badge bg-white-400 text-secondary-dark ms-2">+
                                                            80%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </main>

            </div>
        </div>
        <!-- Body main section ends -->


        <!-- tap on top -->
        <div class="go-top">
            <span class="progress-value">
                <i class="ti ti-chevron-up"></i>
            </span>
        </div>


        <!-- Footer Section starts-->
        <footer>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9 col-12">
                        <ul class="footer-text">
                            <li>
                                <p class="mb-0">Copyright © 2025 axelit. All rights reserved 💖</p>
                            </li>
                            <li><a href="#"> V1.0.0 </a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <ul class="footer-text text-end">
                            <li><a href="mailto:teqlathemes@gmail.com."> Need Help <i class="ti ti-help"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer Section ends-->
    </div>


    <!-- modal -->

    <div class="modal" data-bs-backdrop="static" id="welcomeCard">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content welcome-card">

                <div class="modal-body p-0">
                    <div class="text-center position-relative welcome-card-content z-1 p-3">
                        <div class="text-end position-relative z-1">
                            <i class="ti ti-x fs-5 text-dark f-w-600" data-bs-dismiss="modal"></i>
                        </div>
                        <h2 class="f-w-700 text-primary-dark mb-0"><span>Welcome!</span> <img alt="gif"
                                class="w-45 d-inline align-baseline"
                                src="{{ url('') }}/assets/images/dashboard/ecommerce-dashboard/celebration.gif">
                        </h2>

                        <div class="modal-img-box">
                            <img alt="img" class=" img-fluid"
                                src="{{ url('') }}/assets/images/modals/welcome-1.png">
                        </div>
                        <div class="modal-btn mb-4">
                            <button class="btn btn-primary text-white btn-sm rounded" data-bs-dismiss="modal"
                                type="button">
                                Get Started
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!--customizer-->
    <div id="customizer"></div>

    <!-- latest jquery-->
    <script src="{{ url('') }}/assets/js/jquery-3.6.3.min.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ url('') }}/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Simple bar js-->
    <script src="{{ url('') }}/assets/vendor/simplebar/simplebar.js"></script>

    <!-- apexcharts -->
    <script src="{{ url('') }}/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/apexcharts/column/dayjs.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/apexcharts/column/quarterOfYear.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/apexcharts/timelinechart/moment.min.js"></script>

    <!-- Customizer js-->
    <script src="{{ url('') }}/assets/js/customizer.js"></script>

    <!-- phosphor js -->
    <script src="{{ url('') }}/assets/vendor/phosphor/phosphor.js"></script>

    <!-- slick-file -->
    <script src="{{ url('') }}/assets/vendor/slick/slick.min.js"></script>

    <!-- filepond -->
    <script src="{{ url('') }}/assets/vendor/filepond/file-encode.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/filepond/validate-size.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/filepond/validate-type.js"></script>
    <script src="{{ url('') }}/assets/vendor/filepond/exif-orientation.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/filepond/image-preview.min.js"></script>
    <script src="{{ url('') }}/assets/vendor/filepond/filepond.min.js"></script>

    <!-- Project Dashboard js-->
    <script src="{{ url('') }}/assets/js/project_dashboard.js"></script>

    <!-- App js-->
    <script src="{{ url('') }}/assets/js/script.js"></script>

</body>


<!-- Mirrored from phpstack-1384472-5121645.cloudwaysapps.com/template/html/axelit/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 11 Sep 2025 06:58:29 GMT -->

</html>
