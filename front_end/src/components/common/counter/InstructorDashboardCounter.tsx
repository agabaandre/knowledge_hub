"use client"
import React, { useState } from "react";
import CountUp from "react-countup";
import { InView } from "react-intersection-observer";

export interface CountUpContentProps {
  countNum: number;
  symbol?: string;
  add_style?: boolean;
}

const InstructorDashboardCounter: React.FC<CountUpContentProps> = ({
  countNum,
  symbol,
  add_style = true,
}) => {
const [focus, setFocus] = useState(false);

const visibleChangeHandler = (isVisible: boolean) => {
  if (isVisible && !focus) {
    setFocus(true);
  }
};

return (
  <CountUp
    start={focus ? 0 : countNum}
    end={countNum}
    duration={5}
    separator=""
  >
    {({ countUpRef }) => (
      <div
        className={`bd-promotion-counter-number ${add_style ? "align-items-center justify-content-center" : ""}`}
      >
        {/* Display symbol before the number if present */}
        {symbol && symbol !== "+" && <span className="counter-text">{symbol}</span>}
        
        <span className="counter" ref={countUpRef} />
        
        {/* Display suffix (like +) after the number if present */}
        {symbol && symbol === "+" && <span className="counter-text">{symbol}</span>}
        
        <InView as="span" onChange={visibleChangeHandler} />
      </div>
    )}
  </CountUp>
);
};

export default InstructorDashboardCounter;
