<?php
session_start();
require_once(__DIR__ . '/../src/db_connect.php');

if (isset($_POST['action_type']) && $_POST['action_type']) {
  if ($_POST['action_type'] === 'insert') {
    require(__DIR__ . '/../src/insert_message.php');
  }
  else if ($_POST['action_type'] === 'send') {
    require(__DIR__ . '/../src/insert_comment.php');
  }
  else if ($_POST['action_type'] === 'delete'){
    require(__DIR__ . '/../src/delete_message.php');
  }
}

require(__DIR__ . '/../src/session_values.php');

$stmt = $dbh->query('SELECT * FROM posts ORDER BY created_at DESC;');
// $stmt2 = $dbh->query('SELECT * FROM comments ORDER BY created_at DESC WHERE post_id = $post_id;');

$message_length = $stmt->rowCount();
// $comment_length = $stmt2->rowCount();

function convertTz($datetime_text)
{
  $datetime = new DateTime($datetime_text);
  $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
  return $datetime->format('Y/m/d H:i:s');
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex" />
  <title>Ask Anything</title>
  <link rel="stylesheet" href="./assets/main.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>


<body>
  <div class="container">
    <p class="page-title">なんでもきいて</p>
    <hr class="page-divider" />
    <?php if ($messages['action_success_text'] !== '') { ?>
      <div class="action-success-area"><?php echo $messages['action_success_text']; ?></div>
    <?php } ?>

    <?php if ($messages['action_error_text'] !== '') { ?>
      <div class="action-failed-area"><?php echo $messages['action_error_text']; ?></div>
    <?php } ?>

    <div class="form-cover">
      <form action="/" method="post">
        <label class="form-label" for="author_name">ニックネーム</label>
        <input type="text" name="author_name" maxlength="40" value="<?php echo htmlspecialchars($messages['input_pre_author_name'], ENT_QUOTES); ?>" class="form-control input-author-name" />

        <?php if ($messages['input_error_author_name'] !== '') { ?>
        <div class="form-input-error">
        <?= $messages['input_error_author_name']; ?>
        </div>
        <?php } ?>

        <label class="form-label" for="message">質問内容<small>(必須)</small></label>
        <textarea type="text" name="message" class="form-control"><?php echo htmlspecialchars($messages['input_pre_message'], ENT_QUOTES); ?></textarea>

        <?php if ($messages['input_error_message'] !== '') { ?>

        <div class="form-input-error">
          <?= $messages['input_error_message']; ?>
        </div>
        <?php } ?>
        <input type="hidden" name="action_type" value="insert" />
        <button type="submit" class="btn btn-primary">投稿する</button>
      </form>
    </div>
    <hr class="page-divider" />

    <div class="message-list-cover">
	    <small>
	   件の投稿
	    </small>

      <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
        <?php $lines = explode("\n", $row['message']); ?>

	    <div class="message-item">
	      <div class="message-title">
	        <div><?php echo htmlspecialchars($row['author_name'], ENT_QUOTES); ?></div>
	        <small><?php echo convertTz($row['created_at']); ?></small>
	        <div class="spacer"></div>
	        <form action="/" method="post" style="text-align:right">
	          <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
	          <input type="hidden" name="action_type" value="delete" />
	          <button type="submit" class="btn"><i class="fa-solid fa-trash icon-trash"></i></button>
	        </form>
	      </div>
        <?php foreach ($lines as $line) { ?>
            <p class="message-line"><?php echo htmlspecialchars($line, ENT_QUOTES); ?></p>
          <?php } ?>
          
          
          <!-- Comment Modal -->
          <div class="modal" id="comment" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title fs-5" id="commentModalLabel">New Comment</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="/" method="post">
                    <div class="mb-3">
                      <label for="reader-name" class="col-form-label">ニックネーム</label>
                      <input type="text" class="form-control" maxlength="40" name="reader_name" <?php echo htmlspecialchars($comments['input_pre_reader_name'], ENT_QUOTES); ?>>
                      <?php if ($comments['input_error_reader_name'] !== '') { ?>
                        <div class="form-input-error">
                          <?= $comments['input_error_reader_name']; ?>
                        </div>
                        <?php } ?>
                      </div>
                      <div class="mb-3">
                        <label for="comment" class="col-form-label">コメント</label>
                        <textarea type="text" class="form-control" name="comment"><?php echo htmlspecialchars($comments['input_pre_comment'], ENT_QUOTES); ?></textarea>
                        <?php if ($comments['input_error_comment'] !== '') { ?>
                          <div class="form-input-error">
                            <?= $comments['input_error_comment']; ?>
                          </div>
                          <?php } ?>
                          <input type="hidden" name="action_type" value="send" />
                          <input type="hidden" name="post_id" id="postID" value="" />
                          <button type="submit" class="btn btn-primary">投稿する</button>
                        </div>
                      </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">とじる</button>
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comment" data-postid="<?php echo $row['id']; ?>">コメントする</button>


          <!-- Comment List Modal -->
          <div class="modal" id="commentList" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title fs-5" id="commentModalLabel">Comment List</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-comment">
                <div class="message-list-cover">
                    <div id="commentContent"> 
                       
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">とじる</button>
                </div>
              </div>
            </div>
          </div>
            <input type="hidden" name="post_id_comment" id="postIDForComment" value=""/>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#commentList" data-postidforcomment="<?php echo $row['id']; ?>">コメント見る</button>
	    </div>
      <?php } ?>
	  </div>
  	
	  
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/axios@1.1.2/dist/axios.min.js"></script>                       

  <script>
    $(document).ready(function () {
    $('#comment').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var post_id = button.data('postid');
        console.log(post_id);
        $('#postID').val(post_id);
    });

    // ------------------------------------------------------------------------------

    $('#commentList').on('click', function () {
        var button = $(event.relatedTarget);
        var post_id = button.data('postidforcomment');
        console.log(post_id);
        $('#postIDForComment').val(post_id);

        
        const app = createApp({
          data() {
            return { 
              commments: [],
            }
          },
          methods:{
            fetchComment: function(){
              axios.post('/../src/get_comments.php', {
                postRequest: post_id,
              })
              .then(function (response) {
                app.comments = response.data;
                console.log(response);
              })
              .catch(function (error) {
                console.log(error);
              });
            }
          },
          created:function(){
            this.fetchComment();
          }
        })
      })
    });
  </script>
</body>
</html>



