import React from 'react';

type CourseLearnListProps = {
    topics: string[];
};

const learnTopics: string[][] = [
    [
        "Build fully responsive websites using HTML, CSS, and JavaScript",
        "Develop web applications using modern JavaScript frameworks like React",
        "Implement back-end services with Node.js and Express",
    ],
    [
        "Understand RESTful APIs and connect front-end to back-end",
        "Work with databases like MongoDB and MySQL for full-stack applications",
        "Learn how to deploy web applications to the cloud",
    ]
];

const CourseLearnList: React.FC<CourseLearnListProps> = ({ topics }) => (
    <div className="bd-course-details-list">
        <ul>
            {topics.map((topic, index) => (
                <li key={index}>
                    <span className="list-icon success">
                        <i className="fa-solid fa-check"></i>
                    </span>
                    {topic}
                </li>
            ))}
        </ul>
    </div>
);

const CourseWillYouLearn: React.FC = () => {
    return (
        <div className="bd-course-details-content mb-30">
            <h3 className="bd-course-details-content-title">What {`you'll`} learn</h3>
            <div className="bd-course-details-list-box">
                {learnTopics.map((topics, index) => (
                    <CourseLearnList key={index} topics={topics} />
                ))}
            </div>
        </div>
    );
};

export default CourseWillYouLearn;
