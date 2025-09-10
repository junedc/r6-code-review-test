import React, {useEffect, useState} from 'react'

const FRONTEND_WEATHERBIT_API_KEY = import.meta.env.VITE_WEATHERBIT_FORECAST_KEY
const FRONT_END_BASE_URL = import.meta.env.VITE_WEATHERBIT_BASE_URL

async function fetchDirectFromWeatherbit(city) {
    let c = city
    if (city.toLowerCase() === 'bris' || city.toLowerCase() === 'brissy') c = 'Brisbane'
    if (city.toLowerCase() === 'goldcoast') c = 'Gold Coast'
    if (city.toLowerCase() === 'sunshinecoast') c = 'Sunshine Coast'

    const url = `${FRONT_END_BASE_URL}?city=${encodeURIComponent(c)},AU&days=5&key=${encodeURIComponent(FRONTEND_WEATHERBIT_API_KEY)}`
    const res = await fetch(url)
    if (!res.ok) throw new Error('Direct Weatherbit call failed')
    return res.json()
}

export default function Forecast() {
    const [city, setCity] = useState('Brisbane')
    const [data, setData] = useState(null)
    const [error, setError] = useState(null)
    const [loading, setLoading] = useState(false)

    async function load() {
        setLoading(true)
        setError(null)
        setData(null)

        try {
            const r = await fetch(`http://localhost:8000/api/forecast?city=${encodeURIComponent(city)}`)
            if (!r.ok) throw new Error('Backend failed xxx')
            const j = await r.json()
            if (j && j.days && j.days.length > 0) {
                setData(j)
            } else {
                const dj = await fetchDirectFromWeatherbit(city)
                const mapped = (dj.data || []).slice(0, 5).map(d => {
                    const max = typeof d.max_temp === 'number' ? Math.round(d.max_temp) : 0
                    const min = typeof d.min_temp === 'number' ? Math.round(d.min_temp) : 0
                    const avg = Math.round((max + min) / 2)
                    return {date: d.valid_date, avg, max, min}
                })
                setData({city, days: mapped})
            }
        } catch (e) {
            try {
                const dj = await fetchDirectFromWeatherbit(city)
                const mapped = (dj.data || []).slice(0, 5).map(d => {
                    let max = 0, min = 0
                    if (typeof d.max_temp === 'number') max = Math.round(d.max_temp)
                    if (typeof d.min_temp === 'number') min = Math.round(d.min_temp)
                    const avg = Math.round((max + min) / 2)
                    return {date: d.valid_date, avg, max, min}
                })
                setData({city, days: mapped})
            } catch (e2) {
                setError('Unable to load forecast from both backend and direct API.')
            }
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        load()
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [city])

    return (
        <div style={{border: '1px solid #ddd', borderRadius: 8, padding: 16}}>
            <label htmlFor="city">City:&nbsp;</label>
            <select
                id="city"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                style={{padding: 8, borderRadius: 6, marginBottom: 16}}
            >
                <option>Brisbane</option>
                <option>Gold Coast</option>
                <option>Sunshine Coast</option>
            </select>

            {loading && <p>Loading…</p>}
            {error && <p style={{color: 'red'}}>{error}</p>}

            {data && data.days && data.days.length > 0 && (
                <div style={{overflowX: 'auto'}}>
                    <table style={{borderCollapse: 'collapse', width: '100%'}}>
                        <thead>
                        <tr>
                            <th style={th}>City</th>
                            <th style={th}>Day 1</th>
                            <th style={th}>Day 2</th>
                            <th style={th}>Day 3</th>
                            <th style={th}>Day 4</th>
                            <th style={th}>Day 5</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style={td}>{data.city || city}</td>
                            {data.days.slice(0, 5).map((d, idx) => (
                                <td key={idx} style={td}>
                                    <div><strong>Date:</strong> {d.date}</div>
                                    <div>Avg: {typeof d.avg === 'number' ? d.avg : 'NA'}</div>
                                    <div>Max: {typeof d.max === 'number' ? d.max : 'NA'}</div>
                                    <div>Low: {typeof d.min === 'number' ? d.min : 'NA'}</div>
                                </td>
                            ))}
                        </tr>
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    )
}

const th = {borderBottom: '1px solid #ccc', textAlign: 'left', padding: '8px 6px', fontWeight: 600}
const td = {borderBottom: '1px solid #eee', padding: '8px 6px', verticalAlign: 'top'}
