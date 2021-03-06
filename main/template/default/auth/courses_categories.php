<?php
/* For licensing terms, see /license.txt */

/**
 * View (MVC patter) for courses categories
 * @author Christian Fasanando <christian1827@gmail.com> - Beeznest
 * @package chamilo.auth
 */

if (isset($_REQUEST['action']) && Security::remove_XSS($_REQUEST['action']) !== 'subscribe') {
    $stok = Security::get_token();
} else {
    $stok = $_SESSION['sec_token'];
}

$showCourses = CoursesAndSessionsCatalog::showCourses();
$showSessions = CoursesAndSessionsCatalog::showSessions();
$pageCurrent = isset($pageCurrent) ? $pageCurrent :
    isset($_GET['pageCurrent']) ? intval($_GET['pageCurrent']) :
        1;
$pageLength = isset($pageLength) ? $pageLength :
    isset($_GET['pageLength']) ? intval($_GET['pageLength']) :
        10;
$pageTotal = intval(ceil(intval($countCoursesInCategory) / $pageLength));
$cataloguePagination = $pageTotal > 1 ?
    getCataloguePagination($pageCurrent, $pageLength, $pageTotal) :
    '';
$search_term = isset($search_term) ? $search_term :null;

if ($showSessions && isset($_POST['date'])) {
    $date = $_POST['date'];
} else {
    $date = date('Y-m-d');
}

$userInfo = api_get_user_info();
$code = isset($code) ? $code : null;

?>
<script>
    $(document).ready( function() {
        $('.star-rating li a').on('click', function(event) {
            var id = $(this).parents('ul').attr('id');
            $('#vote_label2_' + id).html("<?php echo get_lang('Loading'); ?>");
            $.ajax({
                url: $(this).attr('data-link'),
                success: function(data) {
                    $("#rating_wrapper_"+id).html(data);
                    if(data == 'added') {
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                    if(data == 'updated') {
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                }
            });
        });

        /*$('.courses-list-btn').toggle(function (e) {
            e.preventDefault();

            var $el = $(this);
            var sessionId = getSessionId(this);

            $el.children('img').remove();
            $el.prepend('<?php echo Display::display_icon('nolines_minus.gif'); ?>');

            $.ajax({
                url: '<?php echo api_get_path(WEB_AJAX_PATH) . 'course.ajax.php' ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    a: 'display_sessions_courses',
                    session: sessionId
                },
                success: function (response){
                    var $container = $el.prev('.course-list');
                    var $courseList = $('<ul>');
                    $.each(response, function (index, course) {
                        $courseList.append('<li><div><strong>' + course.name + '</strong><br>' + course.coachName + '</div></li>');
                    });

                    $container.append($courseList).show(250);
                }
            });
        }, function (e) {
            e.preventDefault();
            var $el = $(this);
            var $container = $el.prev('.course-list');
            $container.hide(250).empty();
            $el.children('img').remove();
            $el.prepend('<?php echo Display::display_icon('nolines_plus.gif'); ?>');
        });*/

        var getSessionId = function (el) {
            var parts = el.id.split('_');
            return parseInt(parts[1], 10);
        };

        <?php if ($showSessions) { ?>
        $('#date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        <?php } ?>
    });
</script>

<div class="row">
    <div class="col-md-4">
        <h5><?php echo get_lang('Search'); ?></h5>
        <?php 
        if ($showCourses) {
            if (!isset($_GET['hidden_links']) || intval($_GET['hidden_links']) != 1) { ?>
            <form class="form-horizontal" method="post" action="<?php echo getCourseCategoryUrl(1, $pageLength, 'ALL', 0, 'subscribe'); ?>">
                <input type="hidden" name="sec_token" value="<?php echo $stok; ?>">
                <input type="hidden" name="search_course" value="1" />
                <div class="input-group">
                    <input class="form-control" type="text" name="search_term" value="<?php echo (empty($_POST['search_term']) ? '' : api_htmlentities(Security::remove_XSS($_POST['search_term']))); ?>" />
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                            <i class="fa fa-search"></i> <?php echo get_lang('Search'); ?>
                        </button>
                    </div>
                </div>
            </form>
            <?php } ?>
    </div>
    <div class="col-md-4">
        <h5><?php echo get_lang('CourseCategories'); ?></h5>
        <?php      
                   
            $webAction = api_get_path(WEB_CODE_PATH).'auth/courses.php';
            $action = (!empty($_REQUEST['action'])?Security::remove_XSS($_REQUEST['action']):'display_courses');
            $pageLength = (!empty($_REQUEST['pageLength'])?intval($_REQUEST['pageLength']):10);
            $pageCurrent = (!empty($_REQUEST['pageCurrent'])?intval($_REQUEST['pageCurrent']):1);
            $form = '<form action="'.$webAction.'" method="GET" class="form-horizontal">';
            $form .= '<input type="hidden" name="action" value="' . $action . '">';
            $form .= '<input type="hidden" name="pageCurrent" value="' . $pageCurrent . '">';
            $form .= '<input type="hidden" name="pageLength" value="' . $pageLength . '">';
            $form .= '<div class="form-group">';
            $form .= '<div class="col-sm-12">';
            $form .= '<select name="category_code" onchange="submit();" class="chzn-select form-control">';
            $codeType = Security::remove_XSS($_REQUEST['category_code']);    
            foreach ($browse_course_categories[0] as $category) {
                $categoryCode = $category['code'];
                $countCourse = $category['count_courses'];
                       
                $form .= '<option '. ($categoryCode == $codeType? 'selected="selected" ':'') .' value="' . $category['code'] . '">' . $category['name'] . ' ( '. $countCourse .' ) </option>';
                if (!empty($browse_course_categories[$categoryCode])) {
                    foreach ($browse_course_categories[$categoryCode] as $subCategory){
                        $subCategoryCode = $subCategory['code'];
                        $form .= '<option '. ($subCategoryCode == $codeType ? 'selected="selected" ':'') .' value="' . $subCategory['code'] . '"> ---' . $subCategory['name'] . ' ( '. $subCategory['count_courses'] .' ) </option>';
                    }
                }
            }
            $form .= '</select>';
            $form .= '</div>';
            $from .= '</form>';
            echo $form;
        ?>
    </div>
    </div>
<?php
        
        if ($showSessions) { ?>
            <div class="col-md-4">
                <h5><?php echo get_lang('Sessions'); ?></h5>
                <a class="btn btn-default btn-block" href="<?php echo getCourseCategoryUrl(1, $pageLength, null, 0, 'display_sessions'); ?>"><?php echo get_lang('SessionList'); ?></a>
            </div>
        <?php } ?>
</div>
 <?php  } ?>
<div class="row"> 
 <?php if ($showCourses && $action != 'display_sessions') { 
            

            if (!empty($message)) {
                Display::display_confirmation_message($message, false);
            }
            if (!empty($error)) {
                Display::display_error_message($error, false);
            }

            if (!empty($content)) {
                echo $content;
            }

            if (!empty($search_term)) {
                echo "<p><strong>".get_lang('SearchResultsFor')." ".Security::remove_XSS($_POST['search_term'])."</strong><br />";
            }

            $ajax_url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=add_course_vote';
            $user_id = api_get_user_id();
            
            if (!empty($browse_courses_in_category)) {
                foreach ($browse_courses_in_category as $course) {
                    $course_hidden = ($course['visibility'] == COURSE_VISIBILITY_HIDDEN);

                    if ($course_hidden) {
                        continue;
                    }

                    $user_registerd_in_course = CourseManager::is_user_subscribed_in_course($user_id, $course['code']);
                    $user_registerd_in_course_as_teacher = CourseManager::is_course_teacher($user_id, $course['code']);
                    $user_registerd_in_course_as_student = ($user_registerd_in_course && !$user_registerd_in_course_as_teacher);
                    $course_public = ($course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD);
                    $course_open = ($course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM);
                    $course_private = ($course['visibility'] == COURSE_VISIBILITY_REGISTERED);
                    $course_closed = ($course['visibility'] == COURSE_VISIBILITY_CLOSED);
                  
                    $course_subscribe_allowed = ($course['subscribe'] == 1);
                    $course_unsubscribe_allowed = ($course['unsubscribe'] == 1);
                    $count_connections = $course['count_connections'];
                    $creation_date = substr($course['creation_date'],0,10);

                    $icon_title = null;
                    $html = null;
                    // display the course bloc
                    $html .= '<div class="col-md-3"><div class="items-course">';

                    // display thumbnail
                    $html .= return_thumbnail($course, $icon_title);

                    // display course title and button bloc
                    $html .= '<div class="items-course-info">';
                    $html .= return_title($course);
                    // display button line
                    $html .= '<div class="btn-toolbar">';
                    // if user registered as student
                    if ($user_registerd_in_course_as_student) {
                        if (!$course_closed) {                        
                            if ($course_unsubscribe_allowed) {
                                $html .= return_unregister_button($course, $stok, $search_term, $code);
                            }
                            $html .= return_already_registered_label('student');
                        }
                        $html .= return_description_button($course, $icon_title);
                    } elseif ($user_registerd_in_course_as_teacher) {
                        // if user registered as teacher
                        //$html .= return_goto_button($course);
                        
                        if ($course_unsubscribe_allowed) {
                            $html .= return_unregister_button($course, $stok, $search_term, $code);
                        }
                        $html .= return_already_registered_label('teacher');
                        $html .= return_description_button($course, $icon_title);
                    } else {
                        // if user not registered in the course
                        if (!$course_closed) {
                            if (!$course_private) {
                                //$html .= return_goto_button($course);
                                if ($course_subscribe_allowed) {
                                    $html .= return_register_button($course, $stok, $code, $search_term);
                                }
                            }
                        }
                        $html .= return_description_button($course, $icon_title);
                    }
                    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    echo $html;
                
                }
            } else {
                if (!isset($_REQUEST['subscribe_user_with_password']) &&
                    !isset($_REQUEST['subscribe_course'])
                ) {
                    Display::display_warning_message(get_lang('ThereAreNoCoursesInThisCategory'));
                }
            } 
        }
    echo $cataloguePagination;

?>   
</div>
   
<?php

/**
 * Display the course catalog image of a course
 * @param $course
 * @param $icon_title
 */
function return_thumbnail($course, $icon_title)
{
    $html = '';
    $title = cut($course['title'], 70);
    // course path
    $course_path = api_get_path(SYS_COURSE_PATH).$course['directory'];
    
    if (file_exists($course_path.'/course-pic.png')) {
        $course_medium_image = api_get_path(WEB_COURSE_PATH).$course['directory'].'/course-pic.png'; // redimensioned image 85x85
    } else {
        $course_medium_image = api_get_path(WEB_IMG_PATH).'session_default.png'; // without picture
    }

    // course image
    
    $html .= '<div class="items-course-image">';
    if (api_get_setting('show_courses_descriptions_in_catalog') == 'true') {
        $html .= '<a class="ajax" href="'.api_get_path(WEB_CODE_PATH).'inc/ajax/course_home.ajax.php?a=show_course_information&amp;code='.$course['code'].'" title="'.$icon_title.'" rel="gb_page_center[778]">';
        $html .= '<img class="img-responsive" src="'.$course_medium_image.'" alt="'.api_htmlentities($title).'" />';
        $html .= '</a>';
    } else {
        $html .= '<img class="img-responsive" src="'.$course_medium_image.'" alt="'.api_htmlentities($title).'"/>';
    }
    $html .= '</div>';  // thumbail
    return $html;
}

/**
 * Display the title of a course in course catalog
 * @param $course
 */
function return_title($course)
{   $html = '';
    $linkCourse = api_get_course_url($course['code']);
    $title = cut($course['title'], 70);
    $ajax_url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=add_course_vote';
    $teachers = CourseManager::get_teacher_list_from_course_code_to_string($course['code']);
    $rating = Display::return_rating_system('star_'.$course['real_id'], $ajax_url.'&amp;course_id='.$course['real_id'], $course['point_info']);    
    $html .=  '<h4 class="title"><a href="' . $linkCourse . '">' . cut($title, 60) . '</a></h4>';
    $html .= '<div class="teachers">'.$teachers.'</div>';
    $html .= '<div class="ranking">'. $rating . '</div>';
    return $html;
}

/**
 * Display the description button of a course in the course catalog
 * @param $course
 * @param $icon_title
 */
function return_description_button($course, $icon_title)
{   
    $title = $course['title'];
    if (api_get_setting('show_courses_descriptions_in_catalog') == 'true') {
        $html = '<a data-title="' . $title . '" class="ajax btn btn-default btn-sm btn-block" href="'.api_get_path(WEB_CODE_PATH).'inc/ajax/course_home.ajax.php?a=show_course_information&amp;code='.$course['code'].'" title="'.$icon_title.'">'.get_lang('Description').'</a>';
    }
    return $html;
}

/**
 * Display the goto course button of a course in the course catalog
 * @param $course
 */
function return_goto_button($course)
{
    $html = ' <a class="btn btn-primary" href="'.api_get_course_url($course['code']).'">'.get_lang('GoToCourse').'</a>';
    return $html;
}

/**
 * Display the already registerd text in a course in the course catalog
 * @param $in_status
 */
function return_already_registered_label($in_status)
{
    $icon = Display::return_icon('teacher.png', get_lang('Teacher'), null, ICON_SIZE_TINY);
    if ($in_status == 'student') {
        $icon = Display::return_icon('user.png', get_lang('Student'), null, ICON_SIZE_TINY);
    }
    $html = Display::div($icon.' '.get_lang("AlreadyRegisteredToCourse"), array('id' => 'register', 'class' => 'user-register'));
    return $html;
}

/**
 * Display the register button of a course in the course catalog
 * @param $course
 * @param $stok
 * @param $code
 * @param $search_term
 */
function return_register_button($course, $stok, $code, $search_term)
{
    $html = ' <a class="btn btn-success btn-block btn-sm" href="'.api_get_self().'?action=subscribe_course&amp;sec_token='.$stok.'&amp;subscribe_course='.$course['code'].'&amp;search_term='.$search_term.'&amp;category_code='.$code.'">'.get_lang('Subscribe').'</a>';
    return $html;
    
}

/**
 * Display the unregister button of a course in the course catalog
 * @param $course
 * @param $stok
 * @param $search_term
 * @param $code
 */
function return_unregister_button($course, $stok, $search_term, $code)
{
    $html = ' <a class="btn btn-primary" href="'. api_get_self().'?action=unsubscribe&amp;sec_token='.$stok.'&amp;unsubscribe='.$course['code'].'&amp;search_term='.$search_term.'&amp;category_code='.$code.'">'.get_lang('Unsubscribe').'</a>';
    return $html;
    
}
