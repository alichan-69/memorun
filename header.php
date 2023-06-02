<header>
    <div class="header_block_content">
        <a href="memo.php">
            <img src="images/memorun_icon.png">
            <h1>memorun</h1>
        </a>
    </div>
    <?php if(isset($_SESSION["user_id"])) echo "<a class='logout' href='./logout.php'>ログアウト</a>";?>
</header>