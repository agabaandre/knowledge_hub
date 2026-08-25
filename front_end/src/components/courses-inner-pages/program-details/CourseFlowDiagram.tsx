import React from 'react';

interface CourseCategory {
    name: string;
    credits: string;
}

const courseCategories: CourseCategory[] = [
    { name: "Language", credits: "6.00" },
    { name: "General Education", credits: "9.00" },
    { name: "Basic Sciences", credits: "11.00" },
    { name: "Mathematics", credits: "12.00" },
    { name: "Other Engineering", credits: "9.00" },
    { name: "Civil Engineering Core", credits: "93.00" },
    { name: "Technical Elective", credits: "14.00" },
    { name: "Undergraduate Thesis", credits: "3.00" },
    { name: "Capstone Project", credits: "3.00" },
    { name: "Internship", credits: "Non-Credit" },
];

const CourseFlowDiagram: React.FC = () => {
    return (
        <div className="bd-course-details-content">
            <h3 className="bd-course-details-content-title">Course Flow Diagram:</h3>
            <p className="description">
                Students are required to select two optional thesis-related theory courses along with their corresponding sessional courses as major courses from any group (I to V). Additionally, two more optional theory courses and a sessional course from the remaining groups (I to V) will be selected as minor courses.
            </p>
            <div className="table-responsive">
                <table className="table table-bordered text-center">
                    <thead className="table-light">
                        <tr>
                            <th scope="col">Categories of Courses</th>
                            <th scope="col">Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        {courseCategories.map((category, index) => (
                            <tr key={index}>
                                <td>{category.name}</td>
                                <td>{category.credits}</td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="table-light">
                            <th>Total</th>
                            <th>160.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    );
};

export default CourseFlowDiagram;
