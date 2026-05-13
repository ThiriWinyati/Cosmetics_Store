<?php
session_start();
require_once "../db_connect.php";
require_once "starRatingForReview.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

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
    <style>
        .review-summary-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .review-stat {
            background: #ffffff;
            border: 1px solid #f0d5e3;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(38, 38, 48, 0.08);
        }

        .review-stat span {
            display: block;
            color: #777;
            font-size: 0.88rem;
            margin-bottom: 6px;
        }

        .review-stat strong {
            color: #d97cb3;
            font-size: 1.5rem;
        }

        .reviews-container {
            display: grid;
            gap: 18px;
        }

        .review-card {
            display: grid;
            grid-template-columns: 116px 1fr auto;
            gap: 18px;
            align-items: center;
            background: #ffffff;
            border: 1px solid #ead8e3;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(38, 38, 48, 0.08);
        }

        .review-product-image {
            width: 116px;
            height: 116px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #f8f8f8;
        }

        .review-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .review-product-title {
            margin: 0 0 6px;
            font-weight: 700;
            color: #222;
        }

        .review-customer {
            color: #d97cb3;
            font-weight: 700;
        }

        .review-text-preview {
            color: #555;
            margin: 10px 0 0;
        }

        .review-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 150px;
        }

        .review-lock-note {
            color: #777;
            font-size: 0.82rem;
            text-align: center;
        }

        .review-modal-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #eee;
            margin-bottom: 16px;
        }

        html[data-theme="dark"] .review-stat,
        html[data-theme="dark"] .review-card {
            background: #1f1f27;
            border-color: #343442;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.38);
        }

        html[data-theme="dark"] .review-stat span,
        html[data-theme="dark"] .review-meta,
        html[data-theme="dark"] .review-text-preview,
        html[data-theme="dark"] .review-lock-note {
            color: #b8bcc6;
        }

        html[data-theme="dark"] .review-product-title {
            color: #f5f5f5;
        }

        html[data-theme="dark"] .review-product-image,
        html[data-theme="dark"] .review-modal-image {
            border-color: #3a3a42;
            background: #28282e;
        }

        @media (max-width: 768px) {
            .review-card {
                grid-template-columns: 1fr;
            }

            .review-product-image {
                width: 100%;
                height: 180px;
            }

            .review-actions {
                min-width: 0;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <div class="container mt-4">
        <?php
        $reviewCount = count($reviews);
        $averageRating = $reviewCount > 0 ? array_sum(array_column($reviews, 'Rating')) / $reviewCount : 0;
        ?>
        <h1 class="text-center">Review Inbox</h1>
        <p class="text-center text-muted mb-4">Monitor customer feedback, product sentiment, and review details.</p>

        <div class="review-summary-bar">
            <div class="review-stat">
                <span>Total Reviews</span>
                <strong><?php echo $reviewCount; ?></strong>
            </div>
            <div class="review-stat">
                <span>Average Rating</span>
                <strong><?php echo number_format($averageRating, 1); ?>/5</strong>
            </div>
            <div class="review-stat">
                <span>Access Mode</span>
                <strong><?php echo $isAdmin ? 'Admin' : 'Read-only'; ?></strong>
            </div>
        </div>

        <div class="reviews-container">
            <?php foreach ($reviews as $review): ?>
                <?php
                $reviewId = (int) $review['Review_ID'];
                $imagePath = !empty($review['image_path'])
                    ? str_replace('../', '/', $review['image_path'])
                    : '/images/default-image.jpg';
                $reviewText = trim($review['Review_Text'] ?? '');
                $previewText = $reviewText !== '' ? mb_strimwidth($reviewText, 0, 130, '...') : 'No written review was provided.';
                ?>
                <div class="review-card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="review-product-image" alt="Product image">

                    <div>
                        <div class="review-meta">
                            <span><?php echo htmlspecialchars($review['Review_Date'] ?? 'No date'); ?></span>
                            <span class="review-customer"><?php echo htmlspecialchars($review['Customer_Name']); ?></span>
                        </div>
                        <h5 class="review-product-title"><?php echo htmlspecialchars($review['Product_Name']); ?></h5>
                        <div><strong>Rating:</strong> <?php echo displayRatingStars($review['Rating']); ?></div>
                        <p class="review-text-preview"><?php echo htmlspecialchars($previewText); ?></p>
                    </div>

                    <div class="review-actions">
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $reviewId; ?>">
                            <i class="fa fa-eye"></i> View Review
                        </button>
                        <?php if ($isAdmin): ?>
                            <button class="btn btn-danger" onclick="deleteReview(<?php echo $reviewId; ?>)">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        <?php else: ?>
                            <a href="adminLogin.php?return_to=viewReviews.php" class="btn btn-secondary">
                                <i class="fa fa-lock"></i> Login to Delete
                            </a>
                            <div class="review-lock-note">Review deletion is locked for visitors.</div>
                        <?php endif; ?>
                    </div>

                    <div class="modal fade" id="reviewModal<?php echo $reviewId; ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?php echo $reviewId; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reviewModalLabel<?php echo $reviewId; ?>">Review Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="review-modal-image" alt="Product image">
                                    <h6>Product: <?php echo htmlspecialchars($review['Product_Name']); ?></h6>
                                    <h6>Customer: <?php echo htmlspecialchars($review['Customer_Name']); ?></h6>
                                    <h6>Rating: <?php echo displayRatingStars($review['Rating']); ?></h6>
                                    <p><?php echo nl2br(htmlspecialchars($reviewText !== '' ? $reviewText : 'No review text')); ?></p>
                                    <small>Date: <?php echo htmlspecialchars($review['Review_Date'] ?? 'Not available'); ?></small>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($isAdmin): ?>
                                        <button type="button" class="btn btn-danger" onclick="deleteReview(<?php echo $reviewId; ?>)">Delete Review</button>
                                    <?php else: ?>
                                        <a href="adminLogin.php?return_to=viewReviews.php" class="btn btn-secondary">
                                            <i class="fa fa-lock"></i> Login Required
                                        </a>
                                    <?php endif; ?>
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
