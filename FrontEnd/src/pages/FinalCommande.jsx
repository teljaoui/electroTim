import React, { useEffect } from 'react'
import { Link } from 'react-router-dom'

export default function FinalCommande() {
    
    useEffect(() => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, []);
    return (
        <>
            <div className="chekout-wrapper home-wrapper-2 py-5">
                <div className="container-xxl">
                    <div className="finale">
                        <h4>order successful</h4>
                        <h5>Card payment is not available at the moment. Payment will be made upon receipt.</h5>
                        <img src="images/truck.gif" width={150} alt="" />
                        <h4>Your order is now being shipped</h4>
                        <br />
                        <Link to="/store" className="buttonbg">Continue Shopping</Link>
                    </div>
                </div>
            </div>
        </>
    )
}
