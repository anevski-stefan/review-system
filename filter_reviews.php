<?php
$ratingOrder = isset($_POST['rating_order']) ? $_POST['rating_order'] : '';
$minimumRating = isset($_POST['minimum_rating']) ? $_POST['minimum_rating'] : '';
$dateOrder = isset($_POST['date_order']) ? $_POST['date_order'] : '';
$prioritizeText = isset($_POST['prioritize_text']) ? $_POST['prioritize_text'] : '';

if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
  $ratingOrder = '';
  $minimumRating = '';
  $dateOrder = '';
  $prioritizeText = '';
  header('Location: index.html');
  exit;
}

$reviewsData = file_get_contents('reviews.json');
$reviews = json_decode($reviewsData, true);


$filteredReviews = array_filter($reviews, function ($review) use ($minimumRating) {
    return $review['rating'] >= $minimumRating;
});


if ($ratingOrder === 'lowest_first') {
    uasort($filteredReviews, function ($a, $b) {
        return $a['rating'] - $b['rating'];
    });
} elseif ($ratingOrder === 'highest_first') {
    uasort($filteredReviews, function ($a, $b) {
        return $b['rating'] - $a['rating'];
    });
}


if ($dateOrder === 'newest_first') {
    usort($filteredReviews, function ($a, $b) {
        return strtotime($b['reviewCreatedOnDate']) - strtotime($a['reviewCreatedOnDate']);
    });
} elseif ($dateOrder === 'oldest_first') {
    usort($filteredReviews, function ($a, $b) {
        return strtotime($a['reviewCreatedOnDate']) - strtotime($b['reviewCreatedOnDate']);
    });
}


if ($prioritizeText === 'yes') {
    uasort($filteredReviews, function ($a, $b) {
        if (empty($a['reviewFullText']) && !empty($b['reviewFullText'])) {
            return 1;
        } elseif (!empty($a['reviewFullText']) && empty($b['reviewFullText'])) {
            return -1;
        } else {
            return 0;
        }
    });
}

if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
  $ratingOrder = '';
  $minimumRating = '';
  $dateOrder = '';
  $prioritizeText = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title>Reviews</title>
  <style>
    .filter-criteria {
      font-size: 1rem;
      font-weight: 100;
      font-family: 'Arial', sans-serif;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col">
        <div>
          <h1 class=text-center>Reviews</h1>
          
        </div>
        
        <div class="mb-3">
          <div>
            <h4>Filter Criteria:</h4>
            <div>
              <?php if (empty($ratingOrder) && empty($minimumRating) && empty($dateOrder) && empty($prioritizeText)) : ?>
                  <div>
                    <div>
                      <div class="alert alert-info">
                        <span class="mr-2">All reviews are being displayed.</span>
                      </div>
                      <a class="btn btn-info" href="?reset=true">Get back to filter</a>
                    </div>
                  </div>
                  <?php else : ?>
                    <div class="d-flex align-items-center">
                      <?php if (!empty($ratingOrder)) : ?>
                        <span class="badge badge-info mr-2 filter-criteria">Rating Order: <?php echo $ratingOrder; ?></span>
                      <?php endif; ?>

                      <?php if (!empty($minimumRating)) : ?>
                        <span class="badge badge-info mr-2 filter-criteria">Minimum Rating: <?php echo $minimumRating; ?></span>
                      <?php endif; ?>

                      <?php if (!empty($dateOrder)) : ?>
                        <span class="badge badge-info mr-2 filter-criteria">Date Order: <?php echo $dateOrder; ?></span>
                      <?php endif; ?>

                      <?php if (!empty($prioritizeText)) : ?>
                        <span class="badge badge-info mr-2 filter-criteria">Prioritize Text: <?php echo $prioritizeText; ?></span>
                      <?php endif; ?>

                      <div class="d-flex w-100 justify-content-end">
                        <div>
                          <a class="btn btn-info align-self-center" href="?reset=true">Get back to filter</a>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
            </div>
          </div>
        </div>




        <?php foreach ($filteredReviews as $review) : ?>
          <div class="card mb-3">
            <div class="card-header">
              Rating: <?php echo $review['rating']; ?>
            </div>
            <div class="card-body">
              <h5 class="card-title">Date: <?php echo $review['reviewCreatedOnDate']; ?></h5>
              <p class="card-text">Text: <?php echo $review['reviewFullText']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>
        
      </div>
    </div>
  </div>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function clearFilter() {
      document.getElementById("rating_order").selectedIndex = 0;
      document.getElementById("minimum_rating").value = "";
      document.getElementById("date_order").selectedIndex = 0;
      document.getElementById("prioritize_text").selectedIndex = 0;
    }
  </script>
</body>

</html>
