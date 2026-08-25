"use client";

import { useEffect, useState, useCallback } from "react";

interface CountdownTimerProps {
  targetDate: string;
}

const CommonCountDownTimer: React.FC<CountdownTimerProps> = ({ targetDate }) => {
  const calculateTimeLeft = useCallback(() => {
    const now = new Date().getTime();
    const endTime = new Date(targetDate).getTime();
    const timeLeft = endTime - now;

    if (timeLeft <= 0) {
      return { days: "00", hours: "00", minutes: "00", seconds: "00" };
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((timeLeft / (1000 * 60)) % 60);
    const seconds = Math.floor((timeLeft / 1000) % 60);

    return {
      days: days < 10 ? `0${days}` : `${days}`,
      hours: hours < 10 ? `0${hours}` : `${hours}`,
      minutes: minutes < 10 ? `0${minutes}` : `${minutes}`,
      seconds: seconds < 10 ? `0${seconds}` : `${seconds}`,
    };
  }, [targetDate]);

  const [timeLeft, setTimeLeft] = useState(calculateTimeLeft());

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft(calculateTimeLeft());
    }, 1000);

    return () => clearInterval(timer);
  }, [calculateTimeLeft]);

  return (
    <div className="bd-countdown mb-30">
      <div className="countdown-item" data-unit="days">
        {timeLeft.days}
        <span>Days</span>
      </div>
      <div className="countdown-item" data-unit="hours">
        {timeLeft.hours}
        <span>Hours</span>
      </div>
      <div className="countdown-item" data-unit="minutes">
        {timeLeft.minutes}
        <span>Minutes</span>
      </div>
      <div className="countdown-item" data-unit="seconds">
        {timeLeft.seconds}
        <span>Seconds</span>
      </div>
    </div>
  );
};

export default CommonCountDownTimer;
