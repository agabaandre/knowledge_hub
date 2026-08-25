"use client";

import Image from "next/image";
import PrayerBgTimeImg from "../../../public/assets/images/bg/prayer-time-bg-2.webp";
import PrayerBg from "../../../public/assets/images/bg/prayer-bg.webp";
import usePrayerTimings from "@/hooks/usePrayerTimings";
import usePrayerCountdown from "@/hooks/usePrayerCountdown";

const formatTime = (time: string): string => {
    const [hour, minute] = time.split(":").map(Number);
    const isAM = hour < 12;
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minute.toString().padStart(2, "0")} ${isAM ? "AM" : "PM"}`;
};

const PrayerTimeArea: React.FC = () => {
    const { timings, loading, error } = usePrayerTimings();

    const { currentPrayer, countdown } = usePrayerCountdown(timings);
    if (!timings) return <p>Loading prayer times...</p>;
    if (loading) return <p>Loading prayer times...</p>;
    if (error) return <p className="text-red-500">{error}</p>;

    return (
        <section
            className="bd-payer-time-area bd-payer-time-bg section-space"
            style={{ backgroundImage: `url(${PrayerBgTimeImg.src})` }}
        >
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-xl-5 col-lg-5 col-md-12">
                        <div className="bd-section-title-wrapper">
                            <span className="bd-section-subtitle text-white">Prayer Times</span>
                            <h2 className="bd-section-title text-white mb-20">
                                Stay Connected with Prayer Times
                            </h2>
                            <p className="bd-section-paragraph text-white">
                                Stay on track with your daily prayers by keeping up with accurate prayer times.
                                Our live prayer schedule helps you remain focused on your spiritual journey
                                throughout the day.
                            </p>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-7 col-md-12">
                        <div className="bd-prayer-wrapper">
                            <Image src={PrayerBg} alt="image" />
                            <div className="bd-prayer-content">
                                <div id="date-container">
                                    {new Intl.DateTimeFormat("en-US", {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    }).format(new Date())}
                                </div>
                                <div id="currentPrayerName">{currentPrayer?.name || "Loading..."}</div>
                                <div id="currentPrayerTime">
                                    {currentPrayer?.time ? formatTime(currentPrayer.time) : "Loading..."}
                                </div>
                                <div id="prayerCountdown">{countdown}</div>
                                <div id="prayerContainer">
                                    {timings &&
                                        Object.entries(timings).map(([name, time]) => (
                                            <div key={name} className={`prayer ${currentPrayer?.name === name ? "next" : ""}`}>
                                                <div>{name}</div>
                                                <div>{formatTime(time)}</div>
                                            </div>
                                        ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default PrayerTimeArea;
