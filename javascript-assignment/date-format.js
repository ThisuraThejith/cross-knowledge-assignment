(() => {
    //Will get all the elements in the document with the particular class
    const elements = document.querySelectorAll('.js-date-format');
    const cache = [];
    elements.forEach((el, i) => {
        const dateString = el.innerHTML.toString();
        const startedDateTime = new Date(dateString);
        update(el, startedDateTime, i);
    });

    //Will check the time difference when time passes and will return the time type and interval as a parameter object
    function getParamsObj(value) {
        const obj = {
            interval: null,
            type: 'date',
        };
        if (value < 60 * 1000) {
            obj.interval = 1000;
            obj.type = 'second';
        }
        if (value >= 60 * 1000 && value < 60 * 60 * 1000) {
            obj.interval = 60 * 1000;
            obj.type = 'minute';
        }
        if (value >= 60 * 60 * 1000 && value < 24 * 60 * 60 * 1000) {
            obj.interval = 60 * 60 * 1000;
            obj.type = 'hour';
        }
        return obj;
    }

    //Will return the display string according to the time value
    function getString(type, value) {
        let string = value;
        switch (type) {
            case 'second': {
                string = string + ' second';
                break;
            }
            case 'minute': {
                string = string + ' minute';
                break;
            }
            case 'hour': {
                string = string + ' hour';
                break;
            }
            default: {
                string = null;
            }
        }
        if (string !== null) {
            if (value > 1) {
                string = string + 's';
            }
            string = string + ' ago';
        }
        return string;
    }

    //Acts as a cache to save the time values and time types of the document elements and is used to change the display
    //values when the time type changes
    function saveCache(el, startedDateTime, paramsObj, cacheIndex) {
        if (paramsObj.interval !== null) {
            cache[cacheIndex] = {
                type: paramsObj.type,
                interval: window.setInterval(() => {
                    update(el, startedDateTime, cacheIndex);
                }, paramsObj.interval)
            };
        }
    }

    //Updates the time value when time passes by
    function update(el, startedDateTime, cacheIndex) {
        const currentDateTime = new Date();
        const timeDifference = (currentDateTime.getTime() - startedDateTime.getTime());
        const paramsObj = getParamsObj(timeDifference);
        const value = parseInt(timeDifference / paramsObj.interval, 10).toFixed(0);
        const stringValue = getString(paramsObj.type, value);
        if (stringValue !== null) {
            el.innerHTML = stringValue;
        } else {
            el.innerHTML = startedDateTime.toISOString();

        }
        if (cache[cacheIndex]) {
            if (cache[cacheIndex].type !== paramsObj.type) {
                window.clearInterval(cache[cacheIndex].interval);
                saveCache(el, startedDateTime, paramsObj, cacheIndex);
            }
        } else {
            saveCache(el, startedDateTime, paramsObj, cacheIndex);
        }
    }
})();
