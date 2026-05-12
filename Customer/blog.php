<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

// Fetch wishlist items
if (isset($_SESSION['customer_id'])) {
    $wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID 
                    FROM favourites f 
                    JOIN products p ON f.Product_ID = p.Product_ID 
                    WHERE f.Customer_ID = ?";
    $stmt = $conn->prepare($wishlistQuery);
    $stmt->execute([$_SESSION['customer_id']]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $wishlistItems = [];
}

// Fetch cart items
if (isset($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = [];
}

$blogPosts = [
    [
        'title' => 'Step-by-Step for Choosing Skin Tone',
        'description' => 'Tips on how to select the perfect foundation for your skin type and tone.',
        'link' => 'https://youtu.be/mhyjeXd4Nm0?si=Xf8BAb7Mfk4eHtUs',
        'videoId' => 'mhyjeXd4Nm0'
    ],
    [
        'title' => 'Pick the Perfect Lipstick for Your Skin Tone',
        'description' => 'Tips on how to select the perfect lipstick color for your skin tone.',
        'link' => 'https://youtu.be/r6qxeo_WwyY?si=9jyO4nsBzIVtTeoq',
        'videoId' => 'r6qxeo_WwyY'
    ],
    [
        'title' => 'Choose your Concealer Shade',
        'description' => 'Tips on how to select the perfect concealer shade for your skin tone.',
        'link' => 'https://youtu.be/dkArQwTPv-Y?si=fciTroq6CIBBbDh_',
        'videoId' => 'dkArQwTPv-Y'
    ],
    [
        'title' => 'How to Apply Concealer',
        'description' => 'Tips on how to apply the concealer.',
        'link' => 'https://youtu.be/yisQVSD3lMU?si=Oaivl6U_8EkIhLVR',
        'videoId' => 'yisQVSD3lMU'
    ],
    [
        'title' => 'Eyeshadow for Every Eye Shape',
        'description' => 'Tips on how to choose eyeshade for your eye shape.',
        'link' => 'https://youtu.be/rV0zgfVEWdg?si=H6rBXvDxG3wcicTL',
        'videoId' => 'rV0zgfVEWdg'
    ],
    [
        'title' => 'Blushes Theory',
        'description' => 'Tips on how to apply your blushes.',
        'link' => 'https://youtu.be/ubhd70KBaZw?si=NKyTt0cuBVJCuxw-',
        'videoId' => 'ubhd70KBaZw'
    ],
    [
        'title' => 'Korean Makeup by Korean Makeup Artist',
        'description' => 'Tips on how to wear makeup like a Korean beauty.',
        'link' => 'https://youtu.be/MRoIFLXWAbo?si=jWEhSCiecUKgo--N',
        'videoId' => 'MRoIFLXWAbo'
    ],
    [
        'title' => 'Everyday Maekup',
        'description' => 'Tutorial for Everyday Makeup.',
        'link' => 'https://youtu.be/FNpt4riezQA?si=k10bGKZDH85IEUB8',
        'videoId' => 'FNpt4riezQA'
    ],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Blogs - Cosmetics Shop</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="container-fluid">

        <section class="blog-header">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                </ol>
            </nav>
            <h4>Makeup Tutorials & Product Tips</h4>
            <p>Discover expert advice on how to apply makeup, choose the best products, and enhance your natural beauty.</p>
        </section>

        <div class="row blogcontent-container">
            <div class="col-md-3 blogcontent-left">
                <h4>Contents</h4>
                <ul class="list-unstyled">
                    <?php foreach ($blogPosts as $post): ?>
                        <li><a href="#<?php echo $post['videoId']; ?>" class="text-primary"><?php echo htmlspecialchars($post['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-9 content-right">
                <?php foreach ($blogPosts as $post): ?>
                    <div class="blog-post" id="<?php echo $post['videoId']; ?>">
                        <iframe src="https://www.youtube.com/embed/<?php echo $post['videoId']; ?>" frameborder="0" allowfullscreen></iframe>
                        <div class="blog-post-content">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p><?php echo htmlspecialchars($post['description']); ?></p>
                            <a href="<?php echo $post['link']; ?>" target="_blank">Watch on YouTube</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>

</html>