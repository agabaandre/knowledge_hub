import React from 'react';

const BookDetails = () => {
    return (
        <table className="bd-product-info-table">
            <tbody>
                <tr>
                    <td><strong>Price:</strong></td>
                    <td><span className="old-price">$30.00</span> <span className="new-price">$27.90</span></td>
                </tr>
                <tr>
                    <td><strong>Publisher:</strong></td>
                    <td>Pamela Dorman Books</td>
                </tr>
                <tr>
                    <td><strong>Publish Date:</strong></td>
                    <td>September 17, 2024</td>
                </tr>
                <tr>
                    <td><strong>Pages:</strong></td>
                    <td>400</td>
                </tr>
                <tr>
                    <td><strong>Dimensions:</strong></td>
                    <td>6.4 X 9.5 X 1.4 inches | 1.3 pounds</td>
                </tr>
                <tr>
                    <td><strong>Language:</strong></td>
                    <td>English</td>
                </tr>
                <tr>
                    <td><strong>Type:</strong></td>
                    <td>Hardcover</td>
                </tr>
                <tr>
                    <td><strong>EAN/UPC:</strong></td>
                    <td>9780593653227</td>
                </tr>
            </tbody>
        </table>
    );
};

export default BookDetails;
