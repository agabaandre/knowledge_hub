"use client";

import { useEffect } from "react";

const useFlashlightAnimation = () => {
  useEffect(() => {
    const elements = document.querySelectorAll<HTMLElement>(".light-effect");

    elements.forEach((element) => {
      const handleMouseMove = (e: MouseEvent) => {
        const rect = element.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        element.style.setProperty("--x", `${x}px`);
        element.style.setProperty("--y", `${y}px`);
      };

      element.addEventListener("mousemove", handleMouseMove);

      return () => {
        element.removeEventListener("mousemove", handleMouseMove);
      };
    });
  }, []);
};

export default useFlashlightAnimation;
