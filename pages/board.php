<?php
require_once("include/captcha.php");
?>
<div class="half-size centered">

	<h3 class="board-title">/<?php print($board["url"]); ?>/</h3>
	<p class="board-description"><?php print($board["desc"]); ?></p>

	<hr>

	<div class="box">
		<div class="boxbar">
			<h3>Create a new thread:</h3>
		</div>
		<div class="boxinner">
			<form action="/api/post.php" enctype="multipart/form-data" method="post">
				<div class="flex-links">
					<input hidden name="type" value="post">
					<input hidden name="board" value="<?php print($splitRequest[1]); ?>">
					<input type="text" name="name" placeholder="Name" value="Anonymous" required>
					<input type="text" name="title" placeholder="Title">
					<textarea name="content" placeholder="Content" required></textarea>
					<input type="file" name="attachment" required>
					<p>Files up to 3MB are allowed.</p><sup>WEBM, WEBP, MP4, PNG, JPG, GIF.</sup>
					<input type="submit" value="Post">
				</div>
			</form>
		</div>
	</div>

    <div class="board-banner">
        <?php
        $errDir = glob("$root\\public/images/banners/*/*.*");
        if(sizeof($errDir) != 0){
            $file = array_rand($errDir);
            preg_match('/.*\/banners\/(.*)\/.*\..*/', $errDir[$file], $out);

            $localPath = path2url($errDir[$file]);
            ?>
            <a href="/<?= $out[1] ?>">
                <img src="<?= $localPath ?>" alt=""/>
            </a>
        <?php } ?>
    </div>


    <hr>

</div>

<?php
$stmt = $db->prepare(<<<SQL
SELECT *,
       (SELECT COALESCE(MAX(timestamp), p.timestamp)
        FROM posts r
        WHERE r.postid IN (SELECT rr.postid FROM posts rr WHERE rr.replyto = p.postid ORDER BY rr.postid LIMIT ?)) AS bump
FROM posts p
WHERE boardurl = ?
  AND type = 'post'
ORDER BY sticky DESC, bump DESC;
SQL);

$stmt->execute([$BUMP_LIMIT,$splitRequest[1]]);
$threads = $stmt->fetchAll();

//print("<pre>");
//print_r($threads);
//print("</pre>");

if ($threads == null) {
?>
	<div class="centered fit">
		<h1>There are no threads in this board.</h1>
	</div>
	</div>
	<?php
} else {
	print("</div>\n<div class=\"catalog\">");
	foreach ($threads as $thread) {
	?>
		<div class="catalog-thread">
			<a href="/<?php print($splitRequest[1] . "/thread/" . $thread["postid"]); ?>">
				<?php
				if ($thread["mime"] == "image/gif") {
					print("<span class='mime'>GIF</span>");
				} elseif($thread["mime"] == "video/webm") {
                    print("<span class='mime'>WEBM</span>");
                } elseif($thread["mime"] == "video/mp4") {
                    print("<span class='mime'>MP4</span>");
                } elseif($thread["mime"] == "image/gif") {
                    print("<span class='mime'>GIF</span>");
                } elseif($thread["sticky"] == 1) {
                    print("<span class='mime'>STICKY</span>");
                }
				?>
                <img loading="lazy" src="/api/getimg.php?id=<?php print($thread["postid"]); ?>&thumb=true"><br>
                <div>
                    <?php
                    if (!empty($thread["title"])) {
                        print("<b>" . htmlspecialchars($thread["title"]) . "</b><br>");
                    }
                    ?>
                    <?php print(htmlspecialchars($thread["text"])); ?>
                </div>
            </a>
        </div>
        <?php
    }
}
?>
</div>

<div class="document">
