<?php
function displayRatingStars($rating)
{
    $fullStars = floor($rating); // Number of full stars
    $halfStar = ($rating - $fullStars) >= 0.5; // Check if half star is needed
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Remaining empty stars

    $stars = '';

    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fa fa-star" style="color: gold;"></i>';
    }

    // Half star
    if ($halfStar) {
        $stars .= '<i class="fa fa-star-half-o" style="color: gold;"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="fa fa-star-o" style="color: gold;"></i>';
    }

    return $stars;
}
