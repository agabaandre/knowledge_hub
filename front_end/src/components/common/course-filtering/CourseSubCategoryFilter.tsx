import { ISubcategoryFilter } from "@/interFace/interFace";
import React from "react";

 interface SidebarSubcategoryProps {
    subcategories: ISubcategoryFilter[];
    onChange?: (selected: string[]) => void;
  }

const SidebarSubcategory: React.FC<SidebarSubcategoryProps> = ({ subcategories, onChange }) => {
    const [selectedSubcategories, setSelectedSubcategories] = React.useState<string[]>([]);

    const handleSubcategoryChange = (id: string) => {
        setSelectedSubcategories((prev) =>
            prev.includes(id)
                ? prev.filter((sub) => sub !== id)
                : [...prev, id]
        );

        // Trigger callback if provided
        if (onChange) {
            const updatedSelection = selectedSubcategories.includes(id)
                ? selectedSubcategories.filter((sub) => sub !== id)
                : [...selectedSubcategories, id];
            onChange(updatedSelection);
        }
    };

    if (!subcategories || subcategories.length === 0) {
        return <p>No subcategories available.</p>;
    }

    return (
        <div className="bd-course-widget widget-subcategory">
            <h5 className="bd-widget-title mb-20">Subcategory</h5>
            <div className="bd-widget-content">
                {subcategories.map((subcategory) => (
                    <div className="checkbox-option" key={subcategory.id}>
                        <input
                            id={subcategory.id}
                            type="checkbox"
                            checked={selectedSubcategories.includes(subcategory.id)}
                            onChange={() => handleSubcategoryChange(subcategory.id)}
                        />
                        <label htmlFor={subcategory.id}>
                            {subcategory.name} <span>({subcategory.count})</span>
                        </label>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default SidebarSubcategory;
