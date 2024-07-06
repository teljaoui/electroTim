import React from 'react';
import Meta from '../components/Meta';
import BreadCrumb from '../components/BreadCrumb';
import { Link } from 'react-router-dom';
import { FaHeartCircleXmark } from "react-icons/fa6";
import { useDispatch, useSelector } from "react-redux";
import { deletewishlistAction } from '../config/Action';
import { useEffect } from 'react';

export default function Wishlist() {
    useEffect(() => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, []);
    const wishlist = useSelector((state) => state.wishlist);
    const dispatch = useDispatch();

    const handleDelete = (id) => {
        if (window.confirm('Are you sure you want to delete this product?')) {
            dispatch(deletewishlistAction(id));
        }
    };

    return (
        <>
            <Meta title={"Wishlist"} />
            <BreadCrumb title="Wishlist" />
            <div className="wishlist-wrapper home-wrapper-2 py-5">
                <div className="container-xxl">
                    <div className="row">
                        {wishlist.length === 0 ? (
                            <div className="cart-empty">
                                <FaHeartCircleXmark className="cart-icon" />
                                <p>Your likes list is empty</p>
                            </div>
                        ) : (
                            wishlist.map((product) => (
                                <div className="col-2 py-2" key={product.id}>
                                    <div className="wishlist-card position-relative text-center">
                                        <img
                                            src="/images/cross.svg"
                                            alt="cross"
                                            className="position-absolute cross img-fluid"
                                            onClick={() => handleDelete(product.id)}
                                        />
                                        <div className="wishlist-card-image">
                                            <Link to={`/product/${product.id}`} >
                                                <img src={`https://admin.electrotim.com/${product.ProductImage} `} alt="" className="img-fluid" width={160} />
                                            </Link>
                                        </div>
                                        <div className="px-2 py-3">
                                            <h5 className="title text-start">
                                                {product.ProductTitle.length > 20 ? product.ProductTitle.substring(0, 20) + '...' : product.ProductTitle}
                                            </h5>
                                            <h6 className="price text-start">{product.ProductPrice} Dhs</h6>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}

                    </div>
                </div>
            </div>
        </>
    );
}
