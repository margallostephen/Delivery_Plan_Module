function schedule(table) {
    const key = "nextTrigger";
    const stored = localStorage.getItem(key);
    const now = new Date();
    let target;

    if (stored) {
        target = new Date(parseInt(stored, 10));
    } else {
        const morning = new Date(now);
        morning.setHours(8, 10, 0, 0);

        const evening = new Date(now);
        evening.setHours(19, 10, 0, 0);

        if (now < morning) {
            target = morning;
        } else if (now < evening) {
            target = evening;
        } else {
            morning.setDate(morning.getDate() + 1);
            target = morning;
        }

        localStorage.setItem(key, target.getTime());
    }

    const delay = target - now;

    if (delay <= 0) {
        autoSetupTable(table);

        if (target.getHours() === 8) {
            target.setHours(19, 10, 0, 0);
        } else {
            target.setHours(8, 10, 0, 0);
            target.setDate(target.getDate() + 1);
        }

        localStorage.setItem(key, target.getTime());
        return schedule(table);
    }

    setTimeout(() => {
        autoSetupTable(table);

        if (target.getHours() === 8) {
            target.setHours(19, 10, 0, 0);
        } else {
            target.setHours(8, 10, 0, 0);
            target.setDate(target.getDate() + 1);
        }

        localStorage.setItem(key, target.getTime());
        schedule(table);
    }, delay);
}
