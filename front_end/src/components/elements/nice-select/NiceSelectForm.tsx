
import React, { useState, useCallback, useRef, KeyboardEvent, MouseEvent } from "react";
import { useClickAway } from "react-use";

interface Option {
  id: number;
  option: string;
}

interface NiceSelectProps {
  options: Option[];
  defaultCurrent: number;
  placeholder?: string;
  className?: string;
  onChange: (item: Option, name: string) => void;
  name: string;
  setSelelectForm:React.Dispatch<React.SetStateAction<string>>;
}

const NiceSelectForm: React.FC<NiceSelectProps> = ({
  options,
  defaultCurrent,
  placeholder,
  className,
  onChange,
  name,
  setSelelectForm
}) => {
  const [open, setOpen] = useState(false);
  const [current, setCurrent] = useState<Option>(options[defaultCurrent]);
  const onClose = useCallback(() => {setOpen(false)}, []);

  const ref = useRef<HTMLDivElement>(null);
  useClickAway(ref, onClose);

  const currentHandler = (item: Option) => {
    setCurrent(item);
    onChange(item, name);
    setSelelectForm(item.option)
    onClose();
  };

  const handleClick = () => {
    setOpen((prev) => !prev);
  };

  const handleKeyPress = (e: KeyboardEvent<HTMLDivElement>) => {
    if (e.key === "Enter") {
      setOpen((prev) => !prev);
    }
  };

  const stopPropagation = (e: MouseEvent | KeyboardEvent) => {
    e.stopPropagation();
  };

  return (
    <div
      className={`nice-select ${className || ""} ${open ? "open" : ""}`}
      role="button"
      tabIndex={0}
      onClick={handleClick}
      onKeyPress={handleKeyPress}
      ref={ref}
    >
      <span className="current">{current?.option || placeholder}</span>
      <ul className="list" role="menubar" onClick={stopPropagation} onKeyPress={stopPropagation}>
        {options?.map((item) => (
          <li
            key={item.id}
            data-value={item.id}
            className={`option ${item.id === current?.id ? "selected focus" : ""}`}
            role="menuitem"
            onClick={() => currentHandler(item)}
            onKeyPress={(e: KeyboardEvent<HTMLLIElement>) => {
              stopPropagation(e);
            }}
          >
            {item.option}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default NiceSelectForm;


