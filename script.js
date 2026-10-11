async function createTrip() {

    // Get values from Create Trip form
    const user_id = document.getElementById("user_id").value;
    const trip_name = document.getElementById("trip_name").value;
    const starting_place = document.getElementById("starting_place").value;
    const destination = document.getElementById("destination").value;
    const start_date = document.getElementById("start_date").value;
    const end_date = document.getElementById("end_date").value;
    const budget = document.getElementById("budget").value;

    // Check required fields
    if (
        trip_name === "" ||
        starting_place === "" ||
        destination === "" ||
        start_date === "" ||
        end_date === "" ||
        budget === ""
    ) {
        alert("Please fill all trip details");
        return;
    }

    // Check dates
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const startDate = new Date(start_date);
    const endDate = new Date(end_date);

    if (startDate < today) {
        alert("Please select today or a future date");
        return;
    }

    if (endDate < startDate) {
        alert("End date must be after start date");
        return;
    }

    // Prepare data
    const formData = new FormData();

    formData.append("user_id", user_id);
    formData.append("trip_name", trip_name);
    formData.append("starting_place", starting_place);
    formData.append("destination", destination);
    formData.append("start_date", start_date);
    formData.append("end_date", end_date);
    formData.append("budget", budget);

    try {

        // Send data to PHP
        const response = await fetch("../backend/createTrip.php", {
            method: "POST",
            body: formData
        });

        const result = await response.text();

        // Show result
        alert(result);

        // If successful, clear form
        if (result.includes("successfully")) {
            document.getElementById("trip_name").value = "";
            document.getElementById("starting_place").value = "";
            document.getElementById("destination").value = "";
            document.getElementById("start_date").value = "";
            document.getElementById("end_date").value = "";
            document.getElementById("budget").value = "";
        }

    } catch (error) {

        console.error(error);
        alert("Error connecting to server");
    }
}

function comparePlannedActual() {
    let planned = parseFloat(document.getElementById("plannedBudget").value);

    if (isNaN(planned) || planned <= 0) {
        document.getElementById("PlannedActualResult").innerHTML =
            "Please enter a valid planned budget";
        return;
    }

    fetch("../backend/getPlannedActual.php")
        .then(response => response.json())
        .then(data => {

            let actual = parseFloat(data.actualExpenses);

            let difference = planned - actual;

            document.getElementById("PlannedActualResult").innerHTML =
                "Planned Budget: ₹" + planned +
                "<br>Actual Expenses: ₹" + actual +
                "<br>Difference: ₹" + difference;
        })
        .catch(error => {
            document.getElementById("PlannedActualResult").innerHTML =
                "Unable to load actual expenses";
        });
}

function loadExpenseAnalysis() {
    fetch("../backend/getExpenses.php")
        .then(response => response.json())
        .then(expenses => {

            let total = 0;
            let highest = 0;
            let categoryTotals = {};

            expenses.forEach(expense => {
                let amount = parseFloat(expense.amount);

                total += amount;

                if (amount > highest) {
                    highest = amount;
                }

                let category = expense.category;

                if (!categoryTotals[category]) {
                    categoryTotals[category] = 0;
                }

                categoryTotals[category] += amount;
            });

            console.log("Total Expenses:", total);
            console.log("Highest Expense:", highest);
            console.log("Category Wise:", categoryTotals);
        })
        .catch(error => {
            console.log("Error loading expenses:", error);
        });
}

function saveExpenseToDatabase() {
    alert("SAVA FUNNCTION RUNNING");
    let expenseName = document.getElementById("expenseName").value;
    let category = document.getElementById("expenseCategory").value;
    let amount = document.getElementById("expenseAmount").value;
    let date = document.getElementById("expenseDate").value;

    let formData = new FormData();

    formData.append("expense_name", expenseName);
    formData.append("category", category);
    formData.append("amount", amount);
    formData.append("expense_date", date);

    fetch("../backend/addExpense.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert(data);
    })
    .catch(error => {
        console.log("Error:", error);
    });
}

function loadExpenses() {
    fetch("../backend/getExpenses.php")
        .then(response => response.json())
        .then(data => {
            const expenseList = document.getElementById("expenseList");

            if (!expenseList) return;

            if (data.error) {
                expenseList.innerHTML = "<p>Unable to load expenses</p>";
                return;
            }

            if (data.length === 0) {
                expenseList.innerHTML = "<p>No expenses found</p>";
                return;
            }

            expenseList.innerHTML = "";

            data.forEach(expense => {
                expenseList.innerHTML += `
                    <div>
                        <strong>${expense.category}</strong>
                        - ₹${expense.amount}
                        - ${expense.expense_date}
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error("Error loading expenses:", error);
        });

        document.addEventListener("DOMContentLoaded", function() {
    loadExpenses();
});

}
            