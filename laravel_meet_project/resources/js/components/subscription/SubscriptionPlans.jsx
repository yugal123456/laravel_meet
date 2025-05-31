import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const SubscriptionPlans = () => {
    const [selectedPlan, setSelectedPlan] = useState(null);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const plans = [
        {
            id: 'basic',
            name: 'Basic',
            price: 29.99,
            features: [
                'Up to 5 bookings per month',
                'Basic room selection',
                'Standard support',
                'Email notifications'
            ]
        },
        {
            id: 'advance',
            name: 'Advance',
            price: 49.99,
            features: [
                'Up to 15 bookings per month',
                'Priority room selection',
                'Priority support',
                'SMS notifications',
                'Booking history'
            ]
        },
        {
            id: 'premium',
            name: 'Premium',
            price: 99.99,
            features: [
                'Unlimited bookings',
                'VIP room access',
                '24/7 support',
                'Real-time notifications',
                'Advanced analytics',
                'Custom features'
            ]
        }
    ];

    const handleSubscribe = async (planId) => {
        setLoading(true);
        setError('');
        try {
            const response = await axios.post('/api/subscribe', { plan: planId });
            if (response.data.success) {
                navigate('/dashboard');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to subscribe to the plan');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container py-5">
            <h2 className="text-center mb-5">Choose Your Plan</h2>

            {error && (
                <div className="alert alert-danger text-center mb-4" role="alert">
                    {error}
                </div>
            )}

            <div className="row row-cols-1 row-cols-md-3 mb-3 text-center">
                {plans.map((plan) => (
                    <div key={plan.id} className="col">
                        <div className={`card mb-4 rounded-3 shadow-sm ${
                            plan.id === 'premium' ? 'border-primary' : ''
                        }`}>
                            <div className={`card-header py-3 ${
                                plan.id === 'premium' ? 'text-bg-primary border-primary' : ''
                            }`}>
                                <h4 className="my-0 fw-normal">{plan.name}</h4>
                            </div>
                            <div className="card-body">
                                <h1 className="card-title pricing-card-title">
                                    ${plan.price}<small className="text-body-secondary fw-light">/mo</small>
                                </h1>
                                <ul className="list-unstyled mt-3 mb-4">
                                    {plan.features.map((feature, index) => (
                                        <li key={index} className="mb-2">
                                            <i className="bi bi-check2 text-success me-2"></i>
                                            {feature}
                                        </li>
                                    ))}
                                </ul>
                                <button
                                    onClick={() => handleSubscribe(plan.id)}
                                    className={`w-100 btn btn-lg ${
                                        plan.id === 'premium'
                                            ? 'btn-primary'
                                            : 'btn-outline-primary'
                                    }`}
                                    disabled={loading}
                                >
                                    {loading && selectedPlan === plan.id ? (
                                        <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    ) : null}
                                    Subscribe
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default SubscriptionPlans;
