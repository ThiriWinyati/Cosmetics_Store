<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
  session_start();
}

$sql = "SELECT 
            p.Product_ID, 
            p.Name, 
            c.Category_Name AS categories, 
            p.Price, 
            a.Name AS admin_users, 
            b.brand_name AS brands, 
            p.Description, 
            p.created_at, 
            p.Image_Path,
            GROUP_CONCAT(pi.image_path) AS images
        FROM products p
        LEFT JOIN categories c ON p.Category_ID = c.Category_ID
        LEFT JOIN admin_users a ON p.Admin_User_ID = a.Admin_User_ID
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN product_images pi ON pi.Product_ID = p.Product_ID
        WHERE p.is_latest = 1
        GROUP BY 
            p.Product_ID, 
            p.Name, 
            c.Category_Name, 
            p.Price, 
            a.Name, 
            b.brand_name, 
            p.Description, 
            p.created_at, 
            p.Image_Path
        ORDER BY p.Product_ID DESC
        LIMIT 8";

try {
  $stmt = $conn->query($sql);
  $status = $stmt->execute();
  if ($status) {
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  echo $e->getMessage();
}

// Query to fetch popular products
$popularProductsQuery = "SELECT 
                            p.Product_ID, 
                            p.Name, 
                            p.Price, 
                            p.Description,   
                            p.is_latest, 
                            p.is_popular, 
                            p.brand_id, 
                            p.Category_ID, 
                            p.Admin_User_ID, 
                            p.created_at, 
                            p.Image_Path,
                            GROUP_CONCAT(pi.image_path) AS images
                        FROM products p
                        LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID
                        WHERE p.is_popular = 1
                        GROUP BY 
                            p.Product_ID, 
                            p.Name, 
                            p.Price, 
                            p.Description,   
                            p.is_latest, 
                            p.is_popular, 
                            p.brand_id, 
                            p.Category_ID, 
                            p.Admin_User_ID, 
                            p.created_at, 
                            p.Image_Path
                        ORDER BY p.Product_ID DESC
                        LIMIT 8";

try {
  $stmt = $conn->query($popularProductsQuery);
  $popularProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo $e->getMessage();
}


// Fetch wishlist items with one image per product
$wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID, MIN(pi.Image_Path) AS Image_Path
FROM favourites f 
JOIN products p ON f.Product_ID = p.Product_ID 
LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID 
WHERE f.Customer_ID = ?
GROUP BY p.Product_ID, p.Name, p.Price";
$stmt = $conn->prepare($wishlistQuery);
$customerId = $_SESSION['customer_id'] ?? null;
$stmt->execute([$customerId]);

$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the add to wishlist button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
  // Get the product ID from the form
  $productID = $_POST['product_id'];

  // Check if the product ID exists in the products table
  $productQuery = "SELECT * FROM products WHERE Product_ID = ?";
  $stmt = $conn->prepare($productQuery);
  $stmt->execute([$productID]);
  $productExists = $stmt->rowCount() > 0;

  if ($productExists) {
    // Check if the customer is logged in
    if (isset($_SESSION['customer_id'])) {
      // Check if the product is already in the wishlist
      $wishlistQuery = "SELECT * FROM favourites WHERE Customer_ID = ? AND Product_ID = ?";
      $stmt = $conn->prepare($wishlistQuery);
      $stmt->execute([$_SESSION['customer_id'], $productID]);
      $wishlistExists = $stmt->rowCount() > 0;

      if (!$wishlistExists) {
        // Add the product to the wishlist
        $addWishlistQuery = "INSERT INTO favourites (Customer_ID, Product_ID) VALUES (?, ?)";
        $stmt = $conn->prepare($addWishlistQuery);
        $stmt->execute([$_SESSION['customer_id'], $productID]);
        // echo "<script>alert('Product added to wishlist successfully!');</script>";
      } else {
        // echo "<script>alert('Product is already in your wishlist.');</script>";
      }
    } else {
      echo "<script>alert('Please log in to add products to your wishlist.');</script>";
      echo "<script>window.location.href = 'user_login.php';</script>";
      exit();
    }
  } else {
    echo "<script>alert('Invalid product ID.');</script>";
  }

  // Update the wishlist dropdown content
  $wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID, MIN(pi.Image_Path) AS Image_Path
  FROM favourites f 
  JOIN products p ON f.Product_ID = p.Product_ID 
  LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID 
  WHERE f.Customer_ID = ?
  GROUP BY p.Product_ID, p.Name, p.Price";
  $stmt = $conn->prepare($wishlistQuery);
  $customerId = $_SESSION['customer_id'] ?? null;
  $stmt->execute([$customerId]);
  $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Update the wishlist dropdown content
  echo "<script>
    var wishlistDropdown = document.getElementById('wishlistDropdown');
    wishlistDropdown.innerHTML = '';
    wishlistDropdown.innerHTML += '<h6 class=\"dropdown-header\">Your Wishlist</h6>';
    if (" . count($wishlistItems) . " > 0) {
      " . implode('', array_map(function ($item) {
    return "wishlistDropdown.innerHTML += '<div class=\"dropdown-item d-flex justify-content-between\"><span>" . htmlspecialchars($item['Name']) . "</span><span>$" . number_format($item['Price'], 2) . "</span></div>';";
  }, $wishlistItems)) . "
    } else {
      wishlistDropdown.innerHTML += '<div class=\"dropdown-item\">Wishlist is empty</div>';
    }
    wishlistDropdown.innerHTML += '<div class=\"dropdown-divider\"></div><a href=\"wishlist.php\" class=\"dropdown-item text-center\">View Wishlist</a>';
  </script>";

  // Update the wishlist quantity
  echo "<script>
    var wishlistQuantity = document.querySelector('.wishlist-quantity');
    wishlistQuantity.textContent = " . count($wishlistItems) . ";
  </script>";
}

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
  <title>Home - Cosmetics Shop</title>
  <style>
    .custom-btn {
      margin-left: 290px;
      margin-top: 140px;
      width: 180px;
      background-color: black;
      color: pink;
    }

    #shop-btn,
    .custom-btn-primary,
    .custom-btn-secondary {
      background-color: #f06c9b;
      /* Custom background color */
      border: none;
      /* Remove border */
      border-radius: 10px;
      /* Rounded corners */
      font-size: 18px;
      /* Custom font size */
      color: #fff;
      /* Text color */
      text-transform: uppercase;
      /* Uppercase text */
      transition: background-color 0.3s ease;
      /* Smooth hover effect */
    }

    #shop-btn:hover,
    .custom-btn-primary:hover,
    .custom-btn-secondary:hover {
      background-color: #e0557a;
      /* Change background on hover */
    }

    .view-more-btn {
      background-color: #007bff;
      border: none;
      border-radius: 5px;
      color: #fff;
      padding: 10px 20px;
      text-transform: uppercase;
      transition: background-color 0.3s ease;
    }

    .view-more-btn:hover {
      background-color: #0056b3;
    }

    .card1:hover .main-image {
      display: none;
    }

    .card1:hover .card-carousel {
      display: block;
    }

    .card-carousel {
      display: none;
    }

    .product-image {
      display: block;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php' ?>

  <div class="card text-black">
    <video autoplay muted loop class="card-img" style="object-fit: cover;">
      <source src="../images/banner.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>

    <div class="card-img-overlay d-flex align-items-center">
      <a href="products.php" class="btn btn-primary btn-lg custom-btn" id="shop-btn">Shop Now</a>
    </div>
  </div>

  </div>

  <h4 class="carousel-title text-center" style="margin-top: 50px;">Available Brands</h4>
  <p class="carousel-title text-center">You can see many types of brands which can be grabbed from our store!</p>

  <div id="brandsCarousel" class="carousel slide" data-bs-ride="carousel">

    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="6" aria-label="Slide 7"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="7" aria-label="Slide 8"></button>
      <button type="button" data-bs-target="#brandsCarousel" data-bs-slide-to="8" aria-label="Slide 9"></button>
    </div>

    <!-- Carousel Content -->
    <div class="carousel-inner">

      <!-- Slide 1 -->
      <div class="carousel-item active">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand1.png" alt="Brand 1">
          </div>
          <div class="brand-card">
            <img src="../images/brand2.webp" alt="Brand 2">
          </div>
          <div class="brand-card">
            <img src="../images/brand3.jpg" alt="Brand 3">
          </div>
          <div class="brand-card">
            <img src="../images/brand4.png" alt="Brand 4">
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand5.jpg" alt="Brand 5">
          </div>
          <div class="brand-card">
            <img src="../images/brand6.jpg" alt="Brand 6">
          </div>
          <div class="brand-card">
            <img src="../images/brand7.png" alt="Brand 7">
          </div>
          <div class="brand-card">
            <img src="../images/brand8.png" alt="Brand 8">
          </div>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand9.png" alt="Brand 9">
          </div>
          <div class="brand-card">
            <img src="../images/brand10.jpg" alt="Brand 10">
          </div>
          <div class="brand-card">
            <img src="../images/brand11.jpg" alt="Brand 11">
          </div>
          <div class="brand-card">
            <img src="../images/brand12.jpg" alt="Brand 12">
          </div>
        </div>
      </div>

      <!-- Slide 4 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand13.png" alt="Brand 13">
          </div>
          <div class="brand-card">
            <img src="../images/brand14.jpg" alt="Brand 14">
          </div>
          <div class="brand-card">
            <img src="../images/brand15.png" alt="Brand 15">
          </div>
          <div class="brand-card">
            <img src="../images/brand16.png" alt="Brand 16">
          </div>
        </div>
      </div>

      <!-- Slide 5 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand17.png" alt="Brand 17">
          </div>
          <div class="brand-card">
            <img src="../images/brand18.png" alt="Brand 18">
          </div>
          <div class="brand-card">
            <img src="../images/brand19.png" alt="Brand 19">
          </div>
          <div class="brand-card">
            <img src="../images/brand20.webp" alt="Brand 20">
          </div>
        </div>
      </div>

      <!-- Slide 6 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand21.png" alt="Brand 21">
          </div>
          <div class="brand-card">
            <img src="../images/brand22.jpg" alt="Brand 22">
          </div>
          <div class="brand-card">
            <img src="../images/brand23.jpg" alt="Brand 23">
          </div>
          <div class="brand-card">
            <img src="../images/brand24.png" alt="Brand 24">
          </div>
        </div>
      </div>

      <!-- Slide 7 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand25.png" alt="Brand 25">
          </div>
          <div class="brand-card">
            <img src="../images/brand26.png" alt="Brand 26">
          </div>
          <div class="brand-card">
            <img src="../images/brand27.png" alt="Brand 27">
          </div>
          <div class="brand-card">
            <img src="../images/brand28.png" alt="Brand 28">
          </div>
        </div>
      </div>

      <!-- Slide 8 -->
      <div class="carousel-item">
        <div class="brand-slide-grid">
          <div class="brand-card">
            <img src="../images/brand29.png" alt="Brand 29">
          </div>
          <div class="brand-card">
            <img src="../images/brand30.jpg" alt="Brand 30">
          </div>
          <div class="brand-card">
            <img src="../images/brand31.jpg" alt="Brand 31">
          </div>
          <div class="brand-card">
            <img src="../images/brand32.png" alt="Brand 32">
          </div>
        </div>
      </div>

      <!-- Slide 9 -->
      <div class="carousel-item">
        <div class="brand-slide-grid brand-slide-grid-last">
          <div class="brand-card">
            <img src="../images/brand33.webp" alt="Brand 33">
          </div>
          <div class="brand-card">
            <img src="../images/brand34.png" alt="Brand 34">
          </div>
          <div class="brand-card">
            <img src="../images/brand35.png" alt="Brand 35">
          </div>
        </div>
      </div>

    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#brandsCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#brandsCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>

  </div>

  <!-- Learn More Section -->
  <div class="container mt-5">
    <div class="row align-items-center">
      <div class="col-md-6 align-items-center">
        <video autoplay loop muted class="w-100" style="object-fit: cover; border: none;">
          <source src="../videos/learn more.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="col-md-6">
        <h4 class="mb-3">Learn More</h4>
        <p>At Charm & Grace, we believe in empowering beauty with a touch of elegance. Our carefully curated collection of cosmetics is designed to bring out the best in you. From luxurious products to trendsetting makeup, we offer a wide range of products to suit every style and preference.</p>
        <a href="about.php" class="btn custom-btn-primary">Go to Learn More</a>
      </div>
    </div>
  </div>

  <!-- Available Coupons Section -->
  <h4 class="carousel-title text-center" style="margin-top: 20px;">Available Coupons</h4>
  <p class="carousel-title text-center">Get promotion for your products by applying below coupons!</p>
  <div class="d-flex justify-content-center align-items-center">
    <div id="couponsCarousel" class="carousel slide" style="flex: 1;">
      <div class="carousel-indicators">
        <?php
        // Initialize $coupons as an empty array if the query fails or returns no results
        $coupons = [];
        try {
          // Query to fetch available coupons
          $couponsQuery = "SELECT * FROM coupons WHERE Valid_From <= NOW() AND Valid_To >= NOW()";
          $stmt = $conn->prepare($couponsQuery);
          $stmt->execute();
          $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          echo "<script>alert('Error fetching coupons: " . $e->getMessage() . "');</script>";
        }

        $numSlides = ceil(count($coupons) / 3);
        for ($i = 0; $i < $numSlides; $i++) {
          $activeClass = ($i === 0) ? 'active' : '';
          echo "<button type='button' data-bs-target='#couponsCarousel' data-bs-slide-to='$i' class='$activeClass' aria-current='" . ($activeClass ? 'true' : 'false') . "' aria-label='Slide $i'></button>";
        }
        ?>
      </div>
      <div class="carousel-inner">
        <?php
        if (!empty($coupons)) {
          $slideIndex = 0;
          foreach ($coupons as $coupon) {
            if ($slideIndex % 3 === 0) {
              echo "<div class='carousel-item " . ($slideIndex === 0 ? 'active' : '') . "'>";
              echo "<div class='d-flex flex-wrap justify-content-center'>";
            }
            echo "<div class='col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-4 mx-2 my-2'>";
            echo "<div class='coupon-card' style='
        background:rgb(255, 243, 246);
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        color: #333;'>";
            // Add space for picture above each card
            echo "<h3 style='font-size: 24px; font-weight: bold; margin-bottom: 10px; color: #333;'>";
            echo htmlspecialchars($coupon['Discount_Percentage']) . "% OFF";
            echo "</h3>";
            echo "<p style='font-size: 16px; margin-bottom: 10px; color: #333;'>";
            echo "Use code: <span style='font-weight: bold; color:rgb(27, 62, 90);'>" . htmlspecialchars($coupon['Coupon_Code']) . "</span>";
            echo "</p>";
            echo "<p style='font-size: 14px; margin-bottom: 10px; color: #333;'>";
            echo "Minimum purchase: $" . htmlspecialchars($coupon['Minimum_Purchase_Amount']);
            echo "</p>";
            echo "<p style='font-size: 14px; margin-bottom: 0; color: #333;'>";
            echo "Expires: " . date('M d, Y', strtotime($coupon['Valid_To']));
            echo "</p>";
            echo "</div>";
            echo "</div>";
            if (($slideIndex + 1) % 3 === 0 || $slideIndex === count($coupons) - 1) {
              echo "</div>";
              echo "</div>";
            }
            $slideIndex++;
          }
        } else {
          echo "<div class='carousel-item active'>";
          echo "<div class='d-flex flex-wrap justify-content-center'>";
          echo "<p>No coupons available at the moment.</p>";
          echo "</div>";
          echo "</div>";
        }
        ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#couponsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#couponsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>

  <!-- New Products Section -->
  <div class="container mt-5 products-section home-products-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="carousel-title text-center">New Arrivals</h4>
      <a href="products.php?filter=new" class="btn btn-link view-more-btn text-decoration-none">View More</a>
    </div>
    <div class="row justify-content-center g-4"> <!-- Adjusted spacing -->
      <?php foreach (array_slice($products, 0, 5) as $product): ?>
        <?php
        $imageArray = explode(',', $product['images']);
        $productID = $product['Product_ID'];
        // Fetch average rating
        $avgRatingQuery = "SELECT AVG(Rating) AS avg_rating, COUNT(Rating) AS total_reviews FROM reviews WHERE Product_ID = ?";
        $stmt = $conn->prepare($avgRatingQuery);
        $stmt->execute([$productID]);
        $avgRatingResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $avgRating = $avgRatingResult['avg_rating'] ? round($avgRatingResult['avg_rating'], 1) : 0;
        $totalReviews = $avgRatingResult['total_reviews'];

        // Add the main product image to the beginning of the images array
        if (!empty($product['Image_Path'])) {
          array_unshift($imageArray, $product['Image_Path']);
        }
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch"> <!-- Adjusted spacing -->
          <div class="card1 border-0 shadow-sm rounded position-relative" style="width: 100%; height: 350px;">
            <div class="card-image-wrapper" style="position: relative;">
              <div class="image-buttons">
                <form method="post" action="user_homeIndex.php">
                  <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                  <button name="add_to_wishlist" class="btn btn-light btn-circle shadow"><i class="fas fa-heart"></i></button>
                </form>
                <a href="viewDetails.php?id=<?php echo $product['Product_ID']; ?>" style="text-decoration: none;">
                  <button class="btn btn-light btn-circle shadow"><i class="fas fa-eye"></i></button>
                </a>
              </div>
              <img src="<?php echo $product['Image_Path']; ?>" class="d-block w-100 rounded-top product-image main-image" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
              <div id="carousel-<?php echo $product['Product_ID']; ?>" class="carousel slide card-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php foreach ($imageArray as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo $image; ?>" class="d-block w-100 rounded-top" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
                    </div>
                  <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            </div>
            <div class="card-body text-center">
              <span class="card-title fw-bold text-truncate"><?php echo $product['Name']; ?></span>
              <div class="mt-2">
                <span class="text-warning">
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <i class="fa<?php echo $i < $avgRating ? 's' : 'r'; ?> fa-star"></i>
                  <?php endfor; ?>
                </span>
                <p class="card-text text-muted"><?php echo $avgRating; ?> stars (<?php echo $totalReviews; ?> reviews)</p>
                <span class="text">$<?php echo $product['Price']; ?></span>
              </div>
              <div class="text-end">

              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- FAQs and Contact Us Section -->
  <div class="container mt-5">
    <div class="row align-items-center">
      <div class="col-md-6 p-4">
        <h4 class="mb-3">You can realize the answers for some common questions in FAQs.</h4>
        <p>However, if you want to ask directly to our team, Contact Us.</p>
        <a href="faq.php" class="btn custom-btn-primary me-2">Go to FAQs</a>
        <a href="contact.php" class="btn custom-btn-secondary">Go to Contact</a>
      </div>
      <div class="col-md-6 d-flex justify-content-end">
        <img src="../images/contact.png" alt="Learn More" class="w-100" style="object-fit: cover; border: none;">

      </div>
    </div>
  </div>

  <!-- Popular Products Section -->
  <div class="container mt-5 justify-content-center align-items-center products-section home-products-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="carousel-title text-center">Popular Products</h4>
      <a href="products.php?filter=popular" class="btn btn-link view-more-btn text-decoration-none">View More</a>
    </div>
    <div class="row justify-content-center g-4"> <!-- Adjusted spacing -->
      <?php foreach (array_slice($popularProducts, 0, 5) as $product): ?>
        <?php
        $imageArray = explode(',', $product['images']);
        $productID = $product['Product_ID'];
        // Fetch average rating
        $avgRatingQuery = "SELECT AVG(Rating) AS avg_rating, COUNT(Rating) AS total_reviews FROM reviews WHERE Product_ID = ?";
        $stmt = $conn->prepare($avgRatingQuery);
        $stmt->execute([$productID]);
        $avgRatingResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $avgRating = $avgRatingResult['avg_rating'] ? round($avgRatingResult['avg_rating'], 1) : 0;
        $totalReviews = $avgRatingResult['total_reviews'];

        // Add the main product image to the beginning of the images array
        if (!empty($product['Image_Path'])) {
          array_unshift($imageArray, $product['Image_Path']);
        }
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch"> <!-- Adjusted spacing -->
          <div class="card1 border-0 shadow-sm rounded position-relative" style="width: 100%; height: 350px;">
            <div class="card-image-wrapper" style="position: relative;">
              <div class="image-buttons">
                <form method="post" action="user_homeIndex.php">
                  <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                  <button name="add_to_wishlist" class="btn btn-light btn-circle shadow"><i class="fas fa-heart"></i></button>
                </form>
                <a href="viewDetails.php?id=<?php echo $product['Product_ID']; ?>" style="text-decoration: none;">
                  <button class="btn btn-light btn-circle shadow"><i class="fas fa-eye"></i></button>
                </a>
              </div>
              <img src="<?php echo $product['Image_Path']; ?>" class="d-block w-100 rounded-top product-image main-image" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
              <div id="carousel-<?php echo $product['Product_ID']; ?>" class="carousel slide card-carousel">
                <div class="carousel-inner">
                  <?php foreach ($imageArray as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo $image; ?>" class="d-block w-100 rounded-top" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
                    </div>
                  <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            </div>
            <div class="card-body text-center">
              <span class="card-title fw-bold text-truncate"><?php echo $product['Name']; ?></span>
              <div class="mt-2">
                <span class="text-warning">
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <i class="fa<?php echo $i < $avgRating ? 's' : 'r'; ?> fa-star"></i>
                  <?php endfor; ?>
                </span>
                <p class="card-text text-muted"><?php echo $avgRating; ?> stars (<?php echo $totalReviews; ?> reviews)</p>
                <span class="text">$<?php echo $product['Price']; ?></span>
              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <style>
    .card1:hover .main-image {
      display: none;
    }

    .card1:hover .card-carousel {
      display: block;
    }

    .card-carousel {
      display: none;
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
