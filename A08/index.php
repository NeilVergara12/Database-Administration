<?php
include("connect.php");

$currentYear = date("Y");

// Use the ternary operator to check if the GET parameters exist, if not, set them to an empty string
$flightNumberFilter = isset($_GET['flightNumber']) ? $_GET['flightNumber'] : '';
$airlineNameFilter = isset($_GET['airlineName']) ? $_GET['airlineName'] : '';
$aircraftTypeFilter = isset($_GET['aircraftType']) ? $_GET['aircraftType'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : '';

// Create the base query
$flightLogsQuery = "SELECT * FROM flightLogs";

$conditions = [];

// Only include filters for the selected criteria
if ($flightNumberFilter != '') {
  $conditions[] = "flightNumber='$flightNumberFilter'";
}

if ($airlineNameFilter != '') {
  $conditions[] = "airlineName='$airlineNameFilter'";
}

if ($aircraftTypeFilter != '') {
  $conditions[] = "aircraftType='$aircraftTypeFilter'";
}

if (count($conditions) > 0) {
  $flightLogsQuery .= " WHERE " . implode(" AND ", $conditions);
}

if ($sort != '') {
  $flightLogsQuery .= " ORDER BY $sort";

  if ($order != '') {
    $flightLogsQuery .= " $order";
  }
}

// Execute the query
$flightLogsResults = mysqli_query($conn, $flightLogsQuery);

// Airline Name query
$airlineNameQuery = "SELECT DISTINCT(airlineName) FROM flightLogs";
$airlineNameResults = mysqli_query($conn, $airlineNameQuery);

// Aircraft Type query
$aircraftTypeQuery = "SELECT DISTINCT(aircraftType) FROM flightLogs";
$aircraftTypeResults = mysqli_query($conn, $aircraftTypeQuery);
?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flight Logs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="icon" href="icon/hatIcon.png" sizes="32x32">
  <style>
    body {
      font-size: 16px;
      background-color: maroon;
    }

    .table th,
    .table td {
      font-size: 14px;
      background-color: white;
      color: black;
    }

    .form-control,
    .btn {
      font-size: 16px;
    }

    .navbar-brand {
      font-size: 30px;
      font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
      color: maroon !important;
    }

    .card {
      background-color: white;
      color: black;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">PUP AIRLINES</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row my-5">
      <div class="col-12 col-md-10 col-lg-8 mx-auto">
        <form method="GET">
          <div class="card p-4 rounded-5">
            <h6>Filter</h6>
            <div class="row gy-3">
              <div class="col-12 col-md-6 col-lg-4">
                <label for="flightNumberSelect" class="form-label">Flight Number</label>
                <input type="text" id="flightNumberSelect" name="flightNumber" class="form-control"
                  value="<?php echo $flightNumberFilter; ?>">
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label for="airlineNameSelect" class="form-label">Airline Name</label>
                <select id="airlineNameSelect" name="airlineName" class="form-control">
                  <option value="">Any</option>
                  <?php
                  if (mysqli_num_rows($airlineNameResults) > 0) {
                    while ($airlineNameRow = mysqli_fetch_assoc($airlineNameResults)) {
                      ?>
                      <option <?php if ($airlineNameFilter == $airlineNameRow['airlineName']) {
                        echo "selected";
                      } ?>
                        value="<?php echo $airlineNameRow['airlineName'] ?>">
                        <?php echo $airlineNameRow['airlineName'] ?>
                      </option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label for="aircraftTypeSelect" class="form-label">Aircraft Type</label>
                <select id="aircraftTypeSelect" name="aircraftType" class="form-control">
                  <option value="">Any</option>
                  <?php
                  if (mysqli_num_rows($aircraftTypeResults) > 0) {
                    while ($aircraftTypeRow = mysqli_fetch_assoc($aircraftTypeResults)) {
                      ?>
                      <option <?php if ($aircraftTypeFilter == $aircraftTypeRow['aircraftType']) {
                        echo "selected";
                      } ?>
                        value="<?php echo $aircraftTypeRow['aircraftType'] ?>">
                        <?php echo $aircraftTypeRow['aircraftType'] ?>
                      </option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label for="sort" class="form-label">Sort By</label>
                <select id="sort" name="sort" class="form-control">
                  <option value="">None</option>
                  <option <?php if ($sort == "flightNumber") {
                    echo "selected";
                  } ?> value="flightNumber">Flight Number
                  </option>
                  <option <?php if ($sort == "departureDatetime") {
                    echo "selected";
                  } ?> value="departureDatetime">
                    Departure Date Time</option>
                  <option <?php if ($sort == "airlineName") {
                    echo "selected";
                  } ?> value="airlineName">Airline Name
                  </option>
                </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label for="order" class="form-label">Order</label>
                <select id="order" name="order" class="form-control">
                  <option <?php if ($order == "ASC") {
                    echo "selected";
                  } ?> value="ASC">Ascending</option>
                  <option <?php if ($order == "DESC") {
                    echo "selected";
                  } ?> value="DESC">Descending</option>
                </select>
              </div>
              <div class="col-12 text-center">
                <button class="btn btn-primary mt-3">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Flight Logs Table -->
    <div class="row my-5">
      <div class="col">
        <div class="card p-4 rounded-5">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Flight Number</th>
                  <th>Departure Airport Code</th>
                  <th>Arrival Airport Code</th>
                  <th>Departure Date Time</th>
                  <th>Arrival Date Time</th>
                  <th>Flight Duration Minutes</th>
                  <th>Airline Name</th>
                  <th>Aircraft Type</th>
                  <th>Passenger Count</th>
                  <th>Ticket Price</th>
                  <th>Credit Card Number</th>
                  <th>Credit Card Type</th>
                  <th>Pilot Name</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (mysqli_num_rows($flightLogsResults) > 0) {
                  while ($flightRow = mysqli_fetch_assoc($flightLogsResults)) {
                    ?>
                    <tr>
                      <td><?php echo $flightRow['flightNumber'] ?></td>
                      <td><?php echo $flightRow['departureAirportCode'] ?></td>
                      <td><?php echo $flightRow['arrivalAirportCode'] ?></td>
                      <td><?php echo $flightRow['departureDatetime'] ?></td>
                      <td><?php echo $flightRow['arrivalDatetime'] ?></td>
                      <td><?php echo $flightRow['flightDurationMinutes'] ?></td>
                      <td><?php echo $flightRow['airlineName'] ?></td>
                      <td><?php echo $flightRow['aircraftType'] ?></td>
                      <td><?php echo $flightRow['passengerCount'] ?></td>
                      <td><?php echo "$" . $flightRow['ticketPrice'] ?></td>
                      <td><?php echo $flightRow['creditCardNumber'] ?></td>
                      <td><?php echo $flightRow['creditCardType'] ?></td>
                      <td><?php echo $flightRow['pilotName'] ?></td>
                    </tr>
                    <?php
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>