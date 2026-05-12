<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charm & Grace: FAQs</title>
</head>

<body>
    <?php include 'navbar.php' ?>

    <div class="container mt-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">FAQs</li>
            </ol>
        </nav>
        <h3>Frequently Asked Questions</h3>
        <div class="accordion" id="faqAccordion">
            <?php
            $faqs = [
                ["question" => "What is Charm & Grace?", "answer" => "Charm & Grace is an online store offering a wide range of beauty products."],
                ["question" => "How can I place an order?", "answer" => "You can place an order by adding items to your cart and proceeding to checkout."],
                ["question" => "What payment methods do you accept?", "answer" => "We accept various payment methods including Credit Cards, PayPal, KBZPay, WavePay and AYA Pay. You can also order the products with Cash on Delivery."],
                ["question" => "How can I track my order?", "answer" => "You can track your order through the Order History Page and you can also contact our admin team to ask about your order."],
                ["question" => "What is your return policy?", "answer" => "We offer a 30-day return policy for unused and unopened products."]
            ];

            foreach ($faqs as $index => $faq) {
                $questionId = "faq" . $index;
            ?>
                <div class="card" id="faq-card">
                    <div class="card-header" id="heading<?php echo $index; ?>">
                        <h5 class="mb-0">
                            <div class="faq-question" data-toggle="collapse" data-target="#<?php echo $questionId; ?>" aria-expanded="true" aria-controls="<?php echo $questionId; ?>">
                                <?php echo htmlspecialchars($faq["question"]); ?>
                                <i class="fa fa-plus"></i>
                            </div>
                        </h5>
                    </div>

                    <div id="<?php echo $questionId; ?>" class="collapse" aria-labelledby="heading<?php echo $index; ?>" data-parent="#faqAccordion">
                        <div class="card-body" id="faqcard-body">
                            <?php echo htmlspecialchars($faq["answer"]); ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <?php include 'footer.php' ?>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.faq-question').click(function() {
                var icon = $(this).find('i');
                if (icon.hasClass('fa-plus')) {
                    icon.removeClass('fa-plus').addClass('fa-minus');
                } else {
                    icon.removeClass('fa-minus').addClass('fa-plus');
                }
            });

            $('#faqAccordion').on('hide.bs.collapse', function(e) {
                $(e.target).prev('.card-header').find('i').removeClass('fa-minus').addClass('fa-plus');
            });

            $('#faqAccordion').on('show.bs.collapse', function(e) {
                $(e.target).prev('.card-header').find('i').removeClass('fa-plus').addClass('fa-minus');
            });
        });
    </script>
</body>

</html>