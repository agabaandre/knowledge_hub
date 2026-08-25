import { useEffect, useState } from "react";

interface PrayerTimings {
    Fajr: string;
    Dhuhr: string;
    Asr: string;
    Maghrib: string;
    Isha: string;
    [key: string]: string;
}

const usePrayerTimings = () => {
    const [timings, setTimings] = useState<PrayerTimings | null>(null);
    const [loading, setLoading] = useState(true); 
    const [error, setError] = useState<string | null>(null); 

    useEffect(() => {
        const fetchTimings = async () => {
            const date = new Date();
            const dd = String(date.getDate()).padStart(2, "0");
            const mm = String(date.getMonth() + 1).padStart(2, "0");
            const yyyy = date.getFullYear();
            const apiURL = `https://api.aladhan.com/v1/timingsByAddress/${dd}-${mm}-${yyyy}?address=Bangladesh`;

            try {
                const response = await fetch(apiURL);
                const data = await response.json();
                if (data?.data?.timings) {
                    setTimings(data.data.timings);
                } else {
                    setError("Could not fetch prayer timings.");
                }
            } catch (err) {
                console.error("Error fetching prayer times:", err);
                setError("Failed to load prayer times. Please check your connection.");
            } finally {
                setLoading(false);
            }
        };

        fetchTimings();
    }, []);

    return { timings, loading, error };
};

export default usePrayerTimings;
