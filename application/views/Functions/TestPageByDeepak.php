<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Status Progress Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #statustablehide {
      display: none;
    }
  </style>
</head>
<body>
  <div class="container my-4">
    <form action="<?= base_url(); ?>Menu/TestPageByDeepak" method="post">
      <div class="mb-3">
        <label class="form-label">Current Year Status</label>
        <select class="form-select" id="statusid" name="cstatusid" onchange="generateTable()">
          <option value="">Select Status</option>
          <option value="1">Open</option>
          <option value="2">Reachout</option>
          <option value="3">Tentative</option>
          <option value="4">Will do Later</option>
          <option value="5">Not Interested</option>
          <option value="6">Positive</option>
          <option value="7">Closure</option>
          <option value="8">OPEN RPEM</option>
          <option value="9">Very Positive</option>
          <option value="10">TTD-Reachout</option>
          <option value="11">WNO-Reachout</option>
          <option value="12">Positive-NAP</option>
          <option value="13">Very Positive-NAP</option>
          <option value="14">On-Boarded</option>
        </select>
      </div>

      <table class="table table-bordered table-striped" id="statustablehide">
        <thead>
          <tr>
            <th>From Status</th>
            <th>To Status</th>
            <th>Target Date</th>
          </tr>
        </thead>
        <tbody id="statusTable"></tbody>
      </table>

      <div class="text-center">
        <button type="submit" class="btn btn-success" id="submitButton" onclick="this.disabled=true; this.innerText='Sending…'; this.form.submit();">
          SUBMIT
        </button>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script>
    function generateTable() {
      const mappings = {
        "1": ["Open", "Tentative"],
        "2": ["Reachout", "Tentative"],
        "4": ["Will do Later", "Tentative"],
        "5": ["Not Interested", "Tentative"],
        "8": ["Open RPEM", "Tentative"],
        "3": ["Tentative", "Closure"],
        "6": ["Positive", "Closure"],
        "7": ["Closure", "On-Boarded"],
        "9": ["Very Positive", "Closure"],
        "10": ["TTD-Reachout", "Closure"],
        "11": ["WNO-Reachout", "Closure"],
        "12": ["Positive-NAP", "Closure"],
        "13": ["Very Positive-NAP", "Closure"]
      };

      const selected = document.getElementById("statusid").value;
      const tableBody = document.getElementById("statusTable");
      const tableWrapper = document.getElementById("statustablehide");

      tableBody.innerHTML = "";
      tableWrapper.style.display = "none";

      if (!mappings[selected]) {
        tableBody.innerHTML = "<tr><td colspan='3' class='text-center text-danger'>No mapping found for selected option</td></tr>";
        return;
      }

      const list = mappings[selected];
      for (let i = 0; i < list.length - 1; i++) {
        const fromStatus = list[i];
        const toStatus = list[i + 1];
        const inputName = fromStatus.toLowerCase().replace(/[\s-]+/g, '_') + '_____' + toStatus.toLowerCase().replace(/[\s-]+/g, '_');

        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${fromStatus}</td>
          <td>${toStatus}</td>
          <td>
          <input type="text" class="form-control" name='timeline_date[]' value="${inputName}" required ">
          <input type="date" class="form-control" name='timeline_date[]' required id="stat_${i}">
          </td>
        `;
        tableBody.appendChild(row);
      }

      tableWrapper.style.display = "table";

      // Chain max dates with 15-day restriction
      for (let i = 0; i < list.length - 2; i++) {
        const currentInput = document.getElementById(`stat_${i}`);
        const nextInput = document.getElementById(`stat_${i + 1}`);

        currentInput.addEventListener("change", function () {
          const selectedDate = new Date(this.value);
          const maxDate = new Date(selectedDate);
          maxDate.setDate(maxDate.getDate() + 15);
          const maxStr = maxDate.toISOString().split("T")[0];
          if (nextInput) nextInput.setAttribute("max", maxStr);
        });
      }
    }
  </script>
</body>
</html>
















