"use client";
import { useEffect, useRef } from "react";
import Typed from "typed.js";

interface TypedTextProps {
  strings: string[];
}

const TypedText: React.FC<TypedTextProps> = ({ strings }) => {
  const el = useRef<HTMLSpanElement | null>(null);
  const typed = useRef<Typed | null>(null);

  useEffect(() => {
    if (el.current) {
      typed.current = new Typed(el.current, {
        strings: strings,
        typeSpeed: 70, 
        backSpeed: 40,
        backDelay: 2000,
        startDelay: 500,
        loop: true,
        showCursor: true, 
        cursorChar: "|", 
      });
    }

    return () => {
      typed.current?.destroy();
    };
  }, [strings]);

  return (
    <span className="typed-wrapper">
      <span ref={el} className="typed-text"></span>
    </span>
  );
};

export default TypedText;

