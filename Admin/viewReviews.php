<?php
session_start();
require_once "../db_connect.php";
require_once "starRatingForReview.php";
require_once "admin_auth.php";

// Fetch all reviews
try {
    $reviewsQuery = "SELECT 
                    r.Review_ID, 
                    p.Name AS Product_Name, 
                    c.Name AS Customer_Name, 
                    r.Rating, 
                    r.Review_Text, 
                    r.Review_Date, 
                    MIN(pi.image_path) AS image_path
                 FROM reviews r 
                 JOIN products p ON r.Product_ID = p.Product_ID 
                 JOIN customers c ON r.Customer_ID = c.Customer_ID
                 LEFT JOIN product_images pi ON p.Product_ID = pi.product_id
                 GROUP BY 
                    r.Review_ID,
                    p.Name,
                    c.Name,
                    r.Rating,
                    r.Review_Text,
                    r.Review_Date
                 ORDER BY r.Review_Date DESC";
    $reviewsStmt = $conn->prepare($reviewsQuery);
    $reviewsStmt->execute();
    $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching reviews: " . $e->getMessage());
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <script src="../Admin/admin_Javascript/forReview.js"></script>
    <title>View Reviews</title>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <div class="container mt-4">
        <h1 class="text-center">View Reviews</h1>

        <div class="reviews-container">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo htmlspecialchars($review['Product_Name']); ?> - <?php echo htmlspecialchars($review['Customer_Name']); ?></h5>
                        </div>
                        <div class="card mb-4">
                            <?php
                            $imagePath = !empty($review['image_path'])
                                ? str_replace('../', '/', $review['image_path'])
                                : '/images/default-image.jpg';
                            ?>

                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                class="card-img-top img-thumbnail" 
                                alt="Product Image" 
                                style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($review['Product_Name']); ?></h5>
                                <p class="card-text"><strong>Rating:</strong> <?php echo displayRatingStars($review['Rating']); ?></p>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $review['Review_ID']; ?>">
                                    View Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review Modal -->
                    <div class="modal fade" id="reviewModal<?php echo $review['Review_ID']; ?>" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reviewModalLabel">Review Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo htmlspecialchars($review['image_path'] ?? 'default-image.jpg'); ?>" class="card-img-top img-thumbnail" alt="Product Image" style="width: 100px; height: 100px;">

                                    <h6>Product: <?php echo htmlspecialchars($review['Product_Name']); ?></h6>
                                    <h6>Customer: <?php echo htmlspecialchars($review['Customer_Name']); ?></h6>
                                    <h6>Rating: <?php echo htmlspecialchars($review['Rating']); ?></h6>
                                    <p><?php echo nl2br(htmlspecialchars($review['Review_Text'] ?? 'No review text')); ?></p>
                                    <small>Date: <?php echo htmlspecialchars($review['Review_Date'] ?? 'Not available'); ?></small>
                                </div>
                                <div class="modal-footer">
                                    <!-- Delete Button -->
                                    <form action="deleteReview.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['Review_ID']; ?>">
                                        <button type="button" class="btn btn-danger" onclick="deleteReview(<?php echo $review['Review_ID']; ?>)">Delete Review</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </div>

</body>

</html>
