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

// Set default timezone to Manila
const MANILA_TZ = "Asia/Manila";

export function useFormatDate() {
    dayjs.locale("en");

    const timeFromNow = (date) => {
        return dayjs(date).tz(MANILA_TZ).fromNow();
    };

    const getUserTimezone = () => {
        return MANILA_TZ;
    };

    const formatDate = (date, localTimeZone = false, format = null) => {
        if (localTimeZone) {
            return dayjs
                .utc(date)
                .tz(MANILA_TZ)
                .format(format ? format : "M/D/YY [at] h:mm A z");
        }
        return dayjs(date).tz(MANILA_TZ).format("MMM DD YYYY h:mm A");
    };

    const formatDateTime = (created_at) => {
        const now = dayjs().tz(MANILA_TZ);
        const createdAtDate = dayjs(created_at).tz(MANILA_TZ);

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

    const dayRangeDate = () => {
        dayjs.localeData().weekStart = 1;
        const date = dayjs().tz(MANILA_TZ);
        let startDate = date.startOf("isoWeek").format("YYYY-MM-DD 00:00:00");
        let endDate = date.endOf("isoWeek").format("YYYY-MM-DD 24:59:59");

        return { start_date: startDate, end_date: endDate };
    };

    const weekRangeDate = () => {
        const date = dayjs().tz(MANILA_TZ);
        let startDate = date.startOf("month").format("YYYY-MM-DD 00:00:00");
        let endDate = date.endOf("month").format("YYYY-MM-DD 24:59:59");

        return { start_date: startDate, end_date: endDate };
    };

    const monthRangeDate = () => {
        const date = dayjs().tz(MANILA_TZ);
        let startDate = date.startOf("year").format("YYYY-MM-DD");
        let endDate = date.endOf("year").format("YYYY-MM-DD");

        return { start_date: startDate, end_date: endDate };
    };

    const humanize = (date) => {
        const humanized = dayjs().tz(MANILA_TZ).to(dayjs(date).tz(MANILA_TZ));
        return humanized.endsWith("ago") ? humanized : `${humanized} ago`;
    };

    const isTodayFn = (date) => {
        return dayjs(date).tz(MANILA_TZ).isToday();
    };

    const isLast24Hour = (date) => {
        return (
            dayjs().tz(MANILA_TZ).diff(dayjs(date).tz(MANILA_TZ), "hour") < 24
        );
    };

    const formatDateRange = (date) => {
        return dayjs(date).tz(MANILA_TZ).format("YYYY/MM/DD");
    };

    const formatGraphDates = (date) => {
        return dayjs(date).tz(MANILA_TZ).format("ddd MMM D YYYY");
    };

    const formatCategoryDates = (date, type) => {
        const d = dayjs(date).tz(MANILA_TZ);
        return type === "last_30" ? d.format("MMM D") : d.format("MMM YYYY");
    };

    return {
        formatDateRange,
        timeFromNow,
        formatDate,
        formatDateTime,
        humanize,
        isToday: isTodayFn,
        dayRangeDate,
        weekRangeDate,
        monthRangeDate,
        getUserTimezone,
        formatGraphDates,
        formatCategoryDates,
        isLast24Hour,
    };
}
