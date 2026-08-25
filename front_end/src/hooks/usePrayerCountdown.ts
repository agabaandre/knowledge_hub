import { useEffect, useState } from "react";

interface Prayer {
  name: string;
  time: string;
}

const usePrayerCountdown = (timings: { [key: string]: string } | null) => {
  const [currentPrayer, setCurrentPrayer] = useState<Prayer | null>(null);
  const [countdown, setCountdown] = useState<string>("");

  useEffect(() => {
    if (!timings) return;

    const updateCurrentPrayer = () => {
      const now = new Date();
      const currentTime = now.getHours() * 60 + now.getMinutes();

      let closestPrayer: Prayer | null = null;
      let closestTimeDiff = Infinity;

      for (const name in timings) {
        const [hour, minute] = timings[name].split(":").map(Number);
        const prayerTime = hour * 60 + minute;
        const timeDiff = prayerTime - currentTime;

        if (timeDiff >= 0 && timeDiff < closestTimeDiff) {
          closestPrayer = { name, time: timings[name] };
          closestTimeDiff = timeDiff;
        }
      }

      setCurrentPrayer(closestPrayer);
    };

    updateCurrentPrayer();
    const interval = setInterval(updateCurrentPrayer, 60 * 1000); // less frequent
    return () => clearInterval(interval);
  }, [timings]);

  useEffect(() => {
    if (!currentPrayer) return;

    const updateCountdown = () => {
      const now = new Date();
      const [nextHour, nextMinute] = currentPrayer.time.split(":").map(Number);

      const nextTime = new Date();
      nextTime.setHours(nextHour, nextMinute, 0, 0);

      if (nextTime.getTime() < now.getTime()) {
        nextTime.setDate(nextTime.getDate() + 1);
      }

      const timeDiff = nextTime.getTime() - now.getTime();
      const secondsLeft = Math.floor(timeDiff / 1000);
      const minutesLeft = Math.floor(secondsLeft / 60);
      const hoursLeft = Math.floor(minutesLeft / 60);

      setCountdown(
        `${hoursLeft}h ${minutesLeft % 60}m ${secondsLeft % 60}s until ${currentPrayer.name}`
      );
    };

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
    return () => clearInterval(interval);
  }, [currentPrayer]);

  return { currentPrayer, countdown };
};

export default usePrayerCountdown;
