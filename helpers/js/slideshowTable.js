let scheduleTimerId = null;

function getNextTarget() {
    const t = new Date();
    t.setHours(8, 30, 0, 0);
    if (t <= new Date()) {
        t.setDate(t.getDate() + 1);
    }
    return t;
}

function schedule(table) {
    const key = "nextTrigger";

    if (scheduleTimerId) {
        clearTimeout(scheduleTimerId);
        scheduleTimerId = null;
    }

    const now = new Date();
    let target;

    const stored = localStorage.getItem(key);
    if (stored) {
        target = new Date(parseInt(stored, 10));
    } else {
        target = getNextTarget();
        localStorage.setItem(key, target.getTime());
    }

    const delay = target - now;

    if (delay <= 0) {
        autoSetupTable(table);
        console.log("table refresh 1");

        target = getNextTarget();
        localStorage.setItem(key, target.getTime());

        scheduleTimerId = setTimeout(() => schedule(table), target - new Date());
        return;
    }

    scheduleTimerId = setTimeout(() => {
        autoSetupTable(table);
        console.log("table refresh 2");

        target = getNextTarget();
        localStorage.setItem(key, target.getTime());
        schedule(table);
    }, delay);

}

function cancelSchedule() {
    if (scheduleTimerId) {
        clearTimeout(scheduleTimerId);
        scheduleTimerId = null;
        console.log("schedule cancelled");
        localStorage.removeItem("nextTrigger");

        if (localStorage.getItem("colRange") === "5DaysRange") {
            $('#toggleExtraDatesBtn').trigger("click");
        }

        if (localStorage.getItem("dataToSet") === "negativeBalRows") {
            $('#toggleRowsBtn').trigger("click");
        }

        $('#toggleAutoPaginateBtn').trigger("click");
    }
}
