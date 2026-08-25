import React from 'react';
import CategoryCheckbox from '@/components/common/course-filtering/CategoryCheckbox';
import LevelCheckbox from '@/components/common/course-filtering/LevelCheckbox';
import PriceFilterCheckbox from '@/components/common/course-filtering/PriceFilterCheckbox';
import RatingFilterCheckbox from '@/components/common/course-filtering/RatingFilterCheckbox';
import InstructorFilter from '@/components/common/course-filtering/InstructorFilter';
import SidebarVideoDuration from '@/components/common/course-filtering/VideoDurationFilter';
import SidebarSubcategory from '@/components/common/course-filtering/CourseSubCategoryFilter';
import LanguageFilter from '@/components/common/course-filtering/LanguageFilter';
import SidebarFeatures from '@/components/common/course-filtering/FeatureFilter';
import SidebarSearch from '@/components/common/course-filtering/SidebarSearch';
import { ICategoryFilter, IFeatureFilter, IInstructorFilter, ILanguageFilter, ILevelFilter, IRatingFilter, ISubcategoryFilter, IVideoDuration, PriceFilter } from '@/interFace/interFace';

interface CourseSidebarProps {
    levels: ILevelFilter[];
    isPriceFilters: PriceFilter[];
    ratingFilters: IRatingFilter[];
    instructors: IInstructorFilter[];
    courseCategoriesData: ICategoryFilter[];
    videoDurationsData: IVideoDuration[];
    subcategoriesData: ISubcategoryFilter[];
    languagesData: ILanguageFilter[];
    featuresData: IFeatureFilter[];
    handleLevelChange: (updatedLevels: ILevelFilter[]) => void;
    handlePriceFilterChange: (updatedFilters: PriceFilter[]) => void;
    handleFilterChange: (stars: number, isChecked: boolean) => void;
    instructorFilterChange: (updatedInstructors: IInstructorFilter[]) => void;
    handleDurationChange: (id: string, isChecked: boolean) => void;
    handleFeatureChange: (id: string, isChecked: boolean) => void;
}

const CourseSidebar: React.FC<CourseSidebarProps> = ({
    levels,
    isPriceFilters,
    ratingFilters,
    instructors,
    courseCategoriesData,
    videoDurationsData,
    subcategoriesData,
    languagesData,
    featuresData,
    handleLevelChange,
    handlePriceFilterChange,
    handleFilterChange,
    instructorFilterChange,
    handleDurationChange,
    handleFeatureChange,
}) => {
    return (
        <>
            <SidebarSearch />
            <div className="bd-course-widget widget-category">
                <h5 className="bd-widget-title mb-20">Categories</h5>
                <CategoryCheckbox categories={courseCategoriesData} />
            </div>
            <div className="bd-course-widget widget-level">
                <h5 className="bd-widget-title mb-20">Level</h5>
                <LevelCheckbox levels={levels} onChange={handleLevelChange} />
            </div>
            <div className="bd-course-widget widget-price">
                <h5 className="bd-widget-title mb-20">Price</h5>
                <PriceFilterCheckbox filters={isPriceFilters} onChange={handlePriceFilterChange} />
            </div>
            <div className="bd-course-widget widget-ratings">
                <h5 className="bd-widget-title mb-20">Ratings</h5>
                <RatingFilterCheckbox filters={ratingFilters} onChange={handleFilterChange} />
            </div>
            <div className="bd-course-widget widget-instructors">
                <h5 className="bd-widget-title mb-20">Instructors</h5>
                <InstructorFilter instructors={instructors} onFilterChange={instructorFilterChange} />
            </div>
            <SidebarVideoDuration durations={videoDurationsData} onDurationChange={handleDurationChange} />
            <SidebarSubcategory
                subcategories={subcategoriesData.slice(0, 6)}
                onChange={() => {}}
            />
            <div className="bd-course-widget widget-language">
                <h5 className="bd-widget-title mb-20">Language</h5>
                <LanguageFilter languages={languagesData} limit={6} />
            </div>
            <SidebarFeatures features={featuresData} onFeatureChange={handleFeatureChange} />
        </>
    );
};

export default CourseSidebar;
