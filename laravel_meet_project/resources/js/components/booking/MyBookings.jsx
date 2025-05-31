import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { format } from 'date-fns';

const MyBookings = () => {
    const [bookings, setBookings] = useState([]);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchBookings();
    }, []);

    const fetchBookings = async () => {
        try {
            const response = await axios.get('/api/my-bookings');
            setBookings(response.data);
            setLoading(false);
        } catch (err) {
            setError('Failed to fetch bookings');
            setLoading(false);
        }
    };

    const handleCancel = async (bookingId) => {
        try {
            await axios.delete(`/api/bookings/${bookingId}`);
            setBookings(bookings.filter(booking => booking.id !== bookingId));
        } catch (err) {
            setError('Failed to cancel booking');
        }
    };

    if (loading) {
        return (
            <div className="container py-5 text-center">
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <div className="container py-5">
            <h2 className="mb-4">My Bookings</h2>

            {error && (
                <div className="alert alert-danger" role="alert">
                    {error}
                </div>
            )}

            {bookings.length === 0 ? (
                <div className="alert alert-info" role="alert">
                    You don't have any bookings yet.
                </div>
            ) : (
                <div className="table-responsive">
                    <table className="table table-striped table-hover">
                        <thead className="table-dark">
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {bookings.map(booking => (
                                <tr key={booking.id}>
                                    <td>{booking.room.name}</td>
                                    <td>{format(new Date(booking.date), 'MMM dd, yyyy')}</td>
                                    <td>
                                        {format(new Date(`2000-01-01T${booking.start_time}`), 'hh:mm a')} -
                                        {format(new Date(`2000-01-01T${booking.end_time}`), 'hh:mm a')}
                                    </td>
                                    <td>{booking.purpose}</td>
                                    <td>
                                        <span className={`badge ${
                                            booking.status === 'confirmed' ? 'bg-success' :
                                            booking.status === 'pending' ? 'bg-warning' :
                                            'bg-danger'
                                        }`}>
                                            {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                        </span>
                                    </td>
                                    <td>
                                        {booking.status !== 'cancelled' && (
                                            <button
                                                onClick={() => handleCancel(booking.id)}
                                                className="btn btn-danger btn-sm"
                                            >
                                                Cancel
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
};

export default MyBookings;
