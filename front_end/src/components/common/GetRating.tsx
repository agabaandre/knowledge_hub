
import React from "react";
interface rattingType {
    averageRating: number;
}
const GetRating = ({ averageRating }: rattingType) => {
    const getRating = (ratingsNum: number) => {
        const roundedRatings = Math.floor(ratingsNum); // Round down to the nearest integer
        const isHalfStar = ratingsNum % 1 !== 0; // Check if there's a decimal part

        const emptyRatingCount = 5 - roundedRatings - (isHalfStar ? 1 : 0);
        const ratings = [];

        for (let i = 0; i < roundedRatings; i++) {
            ratings.push(<i className="fa-solid fa-star" key={`l-${i}`}></i>);
        }

        if (isHalfStar) {
            ratings.push(<i className="fa-solid fa-star-half" key="fa-star-half"></i>);
        }
        for (let i = 0; i < emptyRatingCount; i++) {
            ratings.push(<i className="fa-solid fa-star" key={`p-${i}`}></i>);
        }

        return ratings;
    };
    return (
        <>
            {" "}
            {getRating(averageRating)}{" "}
        </>
    );
};

export default GetRating;
