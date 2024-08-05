import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import timezone from "dayjs/plugin/timezone";
import utc from "dayjs/plugin/utc";
import advanced from "dayjs/plugin/advancedFormat";
import "dayjs/locale/en";
import isocalendar from "dayjs/plugin/isoWeek";
import isToday from "dayjs/plugin/isToday";

dayjs.extend(isToday);
dayjs.extend(relativeTime);
dayjs.extend(timezone);
dayjs.extend(advanced);
dayjs.extend(utc);
dayjs.extend(isocalendar);

const timeZone = dayjs.tz.guess();

export function useFormatDate() {
    dayjs.locale("en");

    const timeFromNow = (date) => {
        return dayjs(date).fromNow();
    };

    const getUserTimezone = () => {
        return dayjs.tz.guess();
    };

    const formatDate = (date, localTimeZone = false, format = null) => {
        if (localTimeZone) {
            return dayjs
                .utc(date)
                .tz(timeZone)
                .format(format ? format : "M/D/YY [at] h:mm A z");
        }
        return dayjs(date).tz("America/Chicago").format("MMM DD YYYY h:mm A");
    };

    const formatDateTime = (created_at) => {
        const now = dayjs();
        const createdAtDate = dayjs(created_at);

        // Calculate the difference in time
        const diffInSeconds = now.diff(createdAtDate, "second");
        const diffInMinutes = now.diff(createdAtDate, "minute");
        const diffInHours = now.diff(createdAtDate, "hour");
        const diffInDays = now.diff(createdAtDate, "day");

        if (diffInSeconds < 60) {
            return `${diffInSeconds} second${
                diffInSeconds !== 1 ? "s" : ""
            } ago`;
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} minute${
                diffInMinutes !== 1 ? "s" : ""
            } ago`;
        } else if (diffInHours < 24) {
            return `${diffInHours} hour${diffInHours !== 1 ? "s" : ""} ago`;
        } else if (diffInDays === 1) {
            return "1 day ago";
        } else {
            return createdAtDate.format("dddd [at] h:mma");
        }
    };

    const dayRangeDate = (timezone) => {
        dayjs.localeData().weekStart = 1;
        const date = dayjs();
        let startDate = date
            .tz(timezone)
            .startOf("isoWeek")
            .format("YYYY-MM-DD 00:00:00");
        let endDate = date
            .tz(timezone)
            .endOf("isoWeek")
            .format("YYYY-MM-DD 24:59:59");

        return { start_date: startDate, end_date: endDate };
    };

    const weekRangeDate = (timezone) => {
        let startDate = dayjs()
            .tz(timezone)
            .startOf("month")
            .format("YYYY-MM-DD 00:00:00");
        let endDate = dayjs()
            .tz(timezone)
            .endOf("month")
            .format("YYYY-MM-DD 24:59:59");

        return { start_date: startDate, end_date: endDate };
    };

    const monthRangeDate = (timezone) => {
        let startDate = dayjs()
            .tz(timezone)
            .startOf("year")
            .format("YYYY-MM-DD");
        let endDate = dayjs().tz(timezone).endOf("year").format("YYYY-MM-DD");

        return { start_date: startDate, end_date: endDate };
    };

    const humanize = (date) => {
        const humanize = dayjs().to(dayjs(date));

        if (!humanize.endsWith("ago")) {
            return humanize + " ago";
        }

        return humanize;
    };

    const isToday = (date) => {
        return dayjs(date).isToday();
    };

    const isLast24Hour = (date) => {
        return dayjs().diff(dayjs(date), "hour") < 24;
    };

    const formatDateRange = (date) => {
        return dayjs(date).format("YYYY/MM/DD");
    };

    const formatGraphDates = (date) => {
        return dayjs(date).format("ddd MMM D YYYY");
    };

    const formatCategoryDates = (date, type) => {
        if (type === "last_30") {
            return dayjs(date).format("MMM D");
        }
        return dayjs(date).format("MMM YYYY");
    };

    return {
        formatDateRange,
        timeFromNow,
        formatDate,
        formatDateTime,
        humanize,
        isToday,
        dayRangeDate,
        weekRangeDate,
        monthRangeDate,
        getUserTimezone,
        formatGraphDates,
        formatCategoryDates,
        isLast24Hour,
    };
}
