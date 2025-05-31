import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const BookingForm = () => {
    const [rooms, setRooms] = useState([]);
    const [formData, setFormData] = useState({
        room_id: '',
        date: '',
        start_time: '',
        end_time: '',
        purpose: ''
    });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        fetchRooms();
    }, []);

    const fetchRooms = async () => {
        try {
            const response = await axios.get('/api/rooms');
            setRooms(response.data);
        } catch (err) {
            setError('Failed to fetch meeting rooms');
        }
    };

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('/api/bookings', formData);
            setSuccess('Room booked successfully!');
            setTimeout(() => navigate('/my-bookings'), 2000);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to book the room');
        }
    };

    return (
        <div className="container py-5">
            <div className="row justify-content-center">
                <div className="col-md-8">
                    <div className="card shadow">
                        <div className="card-body">
                            <h2 className="card-title text-center mb-4">Book a Meeting Room</h2>

                            {error && (
                                <div className="alert alert-danger" role="alert">
                                    {error}
                                </div>
                            )}

                            {success && (
                                <div className="alert alert-success" role="alert">
                                    {success}
                                </div>
                            )}

                            <form onSubmit={handleSubmit}>
                                <div className="mb-3">
                                    <label htmlFor="room_id" className="form-label">Meeting Room</label>
                                    <select
                                        id="room_id"
                                        name="room_id"
                                        className="form-select"
                                        value={formData.room_id}
                                        onChange={handleChange}
                                        required
                                    >
                                        <option value="">Select a room</option>
                                        {rooms.map(room => (
                                            <option key={room.id} value={room.id}>
                                                {room.name} (Capacity: {room.capacity})
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="date" className="form-label">Date</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        id="date"
                                        name="date"
                                        value={formData.date}
                                        onChange={handleChange}
                                        required
                                    />
                                </div>

                                <div className="row">
                                    <div className="col-md-6 mb-3">
                                        <label htmlFor="start_time" className="form-label">Start Time</label>
                                        <input
                                            type="time"
                                            className="form-control"
                                            id="start_time"
                                            name="start_time"
                                            value={formData.start_time}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>

                                    <div className="col-md-6 mb-3">
                                        <label htmlFor="end_time" className="form-label">End Time</label>
                                        <input
                                            type="time"
                                            className="form-control"
                                            id="end_time"
                                            name="end_time"
                                            value={formData.end_time}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="purpose" className="form-label">Purpose</label>
                                    <textarea
                                        className="form-control"
                                        id="purpose"
                                        name="purpose"
                                        rows="3"
                                        value={formData.purpose}
                                        onChange={handleChange}
                                        required
                                    ></textarea>
                                </div>

                                <button type="submit" className="btn btn-primary w-100">
                                    Book Room
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BookingForm;
