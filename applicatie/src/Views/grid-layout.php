<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <header>
        <h1>Here's the header</h1>
    </header>
    <nav>
        <a href="/">nav 1</a>
        <a href="/">nav 2</a>
        <a href="/">nav 3</a>
        <a href="/">nav 4</a>
    </nav>
    <main>
       <div class="container">
            <section>
                <h2>Main content here</h2>
                <ul>
                    <li>1</li>
                    <li>2</li>
                    <li>3</li>
                </ul>
                <?php echo $title; ?>
            </section>
       </div>
    </main>
    <footer>
        This is the footer
    </footer>
</body>
</html>