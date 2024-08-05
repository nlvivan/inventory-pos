import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
import isocalendar from "dayjs/plugin/isoWeek";
dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(isocalendar);

export function useGraphCategories() {
    const last_30 = (salesData = [], timezone) => {
        const currentDate = dayjs();

        const formattedDates = [];
        const thirtyDaysAgo = currentDate.subtract(29, "day");
        for (
            let d = thirtyDaysAgo;
            d.isBefore(currentDate.add(1, "day"));
            d = d.add(1, "day")
        ) {
            const formattedDate = d.format("M/D/YYYY");
            formattedDates.push(formattedDate);
        }

        let array = formattedDates;

        let categories = array.map((arr) => arr);
        let salesValues = [];

        array.forEach((category) => {
            let find = salesData.find((item) => item.date == category);
            if (!find) {
                return salesValues.push({
                    total: 0,
                    date: category,
                });
            }
            return salesValues.push({
                total: find.total,
                date: find.date,
            });
        });

        return {
            category: categories,
            salesGraphData: salesValues,
        };
    };

    const last_year = (salesData = [], timezone) => {
        let array = [
            { key: 1, month: "Jan" },
            { key: 2, month: "Feb" },
            { key: 3, month: "Mar" },
            { key: 4, month: "Apr" },
            { key: 5, month: "May" },
            { key: 6, month: "Jun" },
            { key: 7, month: "Jul" },
            { key: 8, month: "Aug" },
            { key: 9, month: "Sep" },
            { key: 10, month: "Oct" },
            { key: 11, month: "Nov" },
            { key: 12, month: "Dec" },
        ];

        let categories = array.map((arr) => arr.month);
        let values = [];
        let salesValues = [];

        array.forEach((category) => {
            let find = salesData.find((item) => item.month == category.key);
            if (!find) {
                return salesValues.push({
                    total: 0,
                    month: category.key,
                });
            }
            return salesValues.push({
                total: find.total,
                date: find.date,
            });
        });

        return {
            category: categories,
            salesGraphData: salesValues,
        };
    };

    const last_7 = (salesData = [], timezone) => {
        dayjs.localeData().weekStart = 1;
        let values = [];
        const formattedDates = [];
        const currentDate = dayjs();

        for (let i = 6; i >= 0; i--) {
            const date = currentDate.subtract(i, "day");
            const formattedDate = date.format("MM-DD-YYYY");
            formattedDates.push(formattedDate);
        }

        let array = formattedDates;

        let categories = array.map((arr) => arr);
        let salesValues = [];

        array.forEach((category) => {
            let find = salesData.find((item) => item.date == category);
            if (!find) {
                return salesValues.push({
                    total: 0,
                    date: category,
                });
            }

            return salesValues.push({
                total: find.total,
                date: find.date,
            });
        });

        return {
            category: categories,
            salesGraphData: salesValues,
        };
    };

    return {
        last_30,
        last_year,
        last_7,
    };
}
