
import useScrollDirection from "@/hooks/useStickyHeader";
import CircleIcon from "@/svg/Circle";
import React, { useRef, useEffect } from "react";
const BacktoTop = () => {
  const elementRef = useRef<HTMLDivElement>(null);
  const scrollDirection = useScrollDirection(elementRef.current);
  useEffect(() => {
    window.scrollTo({ top: 0 });
  }, []);

  return (
    <>
      {/* -- back to top -- */}
      <div ref={elementRef} className={`backtotop-wrap cursor-pointer ${scrollDirection === "down" ? "active-progress" : ""}`}>
        <CircleIcon/>
      </div>
      {/* -- Backtotop end -- */}
    </>
  );
};

export default BacktoTop;
