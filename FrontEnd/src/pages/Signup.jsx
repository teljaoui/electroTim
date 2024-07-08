import React, { useEffect, useState } from 'react';
import Meta from '../components/Meta';
import BreadCrumb from '../components/BreadCrumb';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

export default function Signup() {
    const [name, setName] = useState('');
    const [lastName, setLastName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [phone, setPhone] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [errorMessages, setErrorMessages] = useState({});
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrorMessages({});

        try {
            const response = await axios.post('https://admin.electrotim.com/api/register', {
                name: name,
                email: email,
                lastName: lastName,
                password: password,
                password_confirmation: confirmPassword,
                phone: phone,
            });

            if (response.status === 200) {
                alert('Registration successful! Please log in.');
                navigate('/login');
            } else {
                throw new Error('Failed to register. Please try again.');
            }
        } catch (error) {
            if (error.response && error.response.data.errors) {
                setErrorMessages(error.response.data.errors);
            } else {
                setErrorMessages({ general: error.message });
            }
        }
    };

    useEffect(() => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, []);

    return (
        <>
            <Meta title={"Signup"} />
            <BreadCrumb title="Signup" />
            <div className="login-wrapper py-5 home-wrapper-2">
                <div className="container-xxl">
                    <div className="row">
                        <div className="col-12">
                            <div className="auth-card">
                                <h3>Create Account</h3>
                                <form className="d-flex flex-column gap-15" onSubmit={handleSubmit}>
                                    {errorMessages.general && <p className="error-message text-danger">{errorMessages.general}</p>}
                                    <div>
                                        <input
                                            type="text"
                                            name="name"
                                            className="form-control"
                                            placeholder="Your Name"
                                            value={name}
                                            onChange={(e) => setName(e.target.value)}
                                            required
                                        />
                                        {errorMessages.name && <span className="text-danger fs-6">{errorMessages.name[0]}</span>}
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            name="lastName"
                                            className="form-control"
                                            placeholder="Your Last Name"
                                            value={lastName}
                                            onChange={(e) => setLastName(e.target.value)}
                                            required
                                        />
                                        {errorMessages.lastName && <span className="text-danger fs-6">{errorMessages.lastName[0]}</span>}
                                    </div>
                                    <div>
                                        <input
                                            type="email"
                                            name="email"
                                            className="form-control"
                                            placeholder="Your Email"
                                            value={email}
                                            onChange={(e) => setEmail(e.target.value)}
                                            required
                                        />
                                        {errorMessages.email && <span className="text-danger fs-6">{errorMessages.email[0]}</span>}
                                    </div>
                                    <div>
                                        <input
                                            type="password"
                                            name="password"
                                            className="form-control"
                                            placeholder="Password"
                                            value={password}
                                            onChange={(e) => setPassword(e.target.value)}
                                            required
                                        />
                                        {errorMessages.password && <span className="text-danger fs-6">{errorMessages.password[0]}</span>}
                                    </div>
                                    <div>
                                        <input
                                            type="password"
                                            name="confirmPassword"
                                            className="form-control"
                                            placeholder="Confirm Password"
                                            value={confirmPassword}
                                            onChange={(e) => setConfirmPassword(e.target.value)}
                                            required
                                        />
                                        {errorMessages.password_confirmation && <span className="text-danger fs-6">{errorMessages.password_confirmation[0]}</span>}
                                    </div>
                                    <div>
                                        <input
                                            type="tel"
                                            name="phone"
                                            className="form-control"
                                            placeholder="Your Phone Number"
                                            value={phone}
                                            onChange={(e) => setPhone(e.target.value)}
                                            required
                                        />
                                        {errorMessages.phone && <span className="text-danger fs-6">{errorMessages.phone[0]}</span>}
                                    </div>
                                    <div className="d-flex flex-column justify-content-center gap-15 align-items-center ">
                                        <button className="button w-25" type="submit">Create</button>
                                        <Link to="/login">Cancel</Link>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
