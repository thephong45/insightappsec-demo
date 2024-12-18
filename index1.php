<?php
include 'database/config.php';

$error = "";
$success = "";

function createSlug($string) {
    $table = array(
        // Character mapping for Vietnamese characters
        'a'=>'a','à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'e'=>'e','è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'i'=>'i','ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'o'=>'o','ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'u'=>'u','ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'y'=>'y','ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'd'=>'d','đ'=>'d',
        'A'=>'a','À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a','Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
        'E'=>'e','È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
        'I'=>'i','Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
        'O'=>'o','Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o','Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o','Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
        'U'=>'u','Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u','Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
        'Y'=>'y','Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y',
        'D'=>'d','Đ'=>'d'
    );
    
    // Replace accented characters with non-accented equivalents
    $string = strtr($string, $table);
    // Replace spaces with hyphens
    $slug = preg_replace('/\s+/', '-', $string);
    // Remove invalid characters
    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
    // Replace multiple hyphens with a single hyphen
    $slug = preg_replace('/-+/', '-', $slug);
    // Convert to lowercase
    $slug = strtolower($slug);

    return $slug;
}


function isDuplicateNews($conn, $slug) {
    $sql = "SELECT id FROM news WHERE slug = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST['title']));
    $slug = createSlug($title);
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $thumbnail = htmlspecialchars(trim($_POST['thumbnail']));

    if (empty($title)) {
        $error = "Vui lòng nhập đầy đủ thông tin";
    } else {
        // Check for duplicate slug
        while (isDuplicateNews($conn, $slug)) {
            $slug .= rand(100000, 999999);
        }

        $sql = "INSERT INTO news (title, slug, content, thumbnail) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $slug, $content, $thumbnail);

        if ($stmt->execute()) {
            $news_id = $stmt->insert_id;
            $news_url = $slug;
            $_SESSION['success'] = "Bài viết đã được tạo thành công, vui lòng xem tại đây 👉 <a href='$news_url'>Xem Bài Viết</a>";
            header("Location: /");
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
    // Đóng kết nối
$conn->close();
?>

<?php
include 'layouts/header.php';
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Viết Báo Pr
        <small>Tạo bài viết pr bản thân</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Viết Báo Pr</li>
      </ol>
    </section>
    <div class="container">
    <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <h1 class="my-4">Viết Báo Pr</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="thumbnail">Ảnh bìa:</label>
                <input type="text" class="form-control" id="thumbnail" name="thumbnail">
                <input type="file" class="form-control-file" id="thumbnail-upload" name="thumbnail-upload" accept="image/png, image/gif, image/jpeg">
            </div>
            <div class="form-group">
                <label for="title">Tiêu đề:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Nội dung:</label>
                <textarea class="form-control" id="content" name="content" rows="20"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">TẠO BÀI VIẾT</button>
        </form>
    </div>
</div>
<?php
include 'layouts/footer.php';
?>

<script>
document.getElementById('thumbnail-upload').addEventListener('change', function (e) {
    var formData = new FormData();
    formData.append('file', e.target.files[0]);

    fetch('upload_handler.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(url => {
        document.getElementById('thumbnail').value = url;
    }).catch(error => console.error('Error:', error));
});
</script>

<script>
tinymce.init({
    selector: '#content',
    plugins: [
        'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
        'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
        'media', 'table', 'emoticons', 'help'
    ],
    toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
    menu: {
        favs: { title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons' }
    },
    menubar: 'favs file edit view insert format tools table help',
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    },
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'upload_tinymce.php');

        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            success(json.location);
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }
});


</script>
