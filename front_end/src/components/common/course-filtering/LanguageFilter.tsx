import { ILanguageFilter} from "@/interFace/interFace";
import React, { useState } from "react";

export interface LanguageFilterProps {
    languages: ILanguageFilter[];
    limit?: number;
  }

const LanguageFilter: React.FC<LanguageFilterProps> = ({ languages, limit = 3 }) => {
    const [selectedLanguages, setSelectedLanguages] = useState<string[]>([]);

    const handleLanguageChange = (language: string) => {
        setSelectedLanguages((prev) =>
            prev.includes(language)
                ? prev.filter((lang) => lang !== language)
                : [...prev, language]
        );
    };

    if (!languages || languages.length === 0) {
        return <p>No languages available.</p>;
    }

    return (
        <div className="bd-widget-content">
            {languages.slice(0, limit).map((language) => {
                const isSelected = selectedLanguages.includes(language.name);
                return (
                    <div className="checkbox-option" key={language.id}>
                        <input
                            id={language.id}
                            type="checkbox"
                            checked={isSelected}
                            onChange={() => handleLanguageChange(language.name)}
                        />
                        <label htmlFor={language.id}>
                            {language.name} <span>({language.count})</span>
                        </label>
                    </div>
                );
            })}
        </div>
    );
};

export default LanguageFilter;
