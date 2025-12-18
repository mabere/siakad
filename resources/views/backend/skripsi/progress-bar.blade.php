<style>
    .zigzag-timeline {
        position: relative;
        padding: 0;
        list-style: none;
        max-width: 100%;
    }

    .zigzag-timeline .timeline-item {
        position: relative;
        margin-bottom: 0px;
        display: flex;
        align-items: center;
    }

    .zigzag-timeline .timeline-item.odd {
        justify-content: flex-start;
    }

    .zigzag-timeline .timeline-item.even {
        justify-content: flex-end;
    }

    .zigzag-timeline .timeline-item::before {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 0;
        bottom: -40px;
        width: 4px;
        background: #e9ecef;
        z-index: 1;
    }

    .zigzag-timeline .timeline-item:last-child::before {
        display: none;
    }

    .zigzag-timeline .timeline-icon {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 15%;
        width: 34px;
        height: 34px;
        background: #fff;
        border: 4px solid;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .zigzag-timeline .timeline-item.completed .timeline-icon {
        border-color: #28a745;
        color: #28a745;
    }

    .zigzag-timeline .timeline-item.current .timeline-icon {
        border-color: #ffc107;
        color: #ffc107;
    }

    .zigzag-timeline .timeline-item.pending .timeline-icon {
        border-color: #6c757d;
        color: #6c757d;
    }

    .zigzag-timeline .timeline-content {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        width: 45%;
    }

    .zigzag-timeline .timeline-item.odd .timeline-content {
        margin-right: 60px;
    }

    .zigzag-timeline .timeline-item.even .timeline-content {
        margin-left: 60px;
    }

    .zigzag-timeline .timeline-content::before {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border: 10px solid transparent;
    }

    .zigzag-timeline .timeline-item.odd .timeline-content::before {
        right: -20px;
        border-left-color: #e9ecef;
    }

    .zigzag-timeline .timeline-item.even .timeline-content::before {
        left: -20px;
        border-right-color: #e9ecef;
    }

    .zigzag-timeline .timeline-content::after {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border: 10px solid transparent;
    }

    .zigzag-timeline .timeline-item.odd .timeline-content::after {
        right: -18px;
        border-left-color: #fff;
    }

    .zigzag-timeline .timeline-item.even .timeline-content::after {
        left: -18px;
        border-right-color: #fff;
    }

    .zigzag-timeline .progress {
        height: 6px;
        background: #e9ecef;
    }

    .zigzag-timeline .progress-bar {
        background: #ffc107;
    }

    @media (max-width: 768px) {

        .zigzag-timeline .timeline-item.odd .timeline-content,
        .zigzag-timeline .timeline-item.even .timeline-content {
            width: 100%;
            margin-left: 40px;
            margin-right: 0;
        }

        .zigzag-timeline .timeline-item.even {
            justify-content: flex-start;
        }

        .zigzag-timeline .timeline-item.odd .timeline-content::before,
        .zigzag-timeline .timeline-item.even .timeline-content::before {
            left: -20px;
            border-right-color: #e9ecef;
            border-left-color: transparent;
        }

        .zigzag-timeline .timeline-item.odd .timeline-content::after,
        .zigzag-timeline .timeline-item.even .timeline-content::after {
            left: -18px;
            border-right-color: #fff;
            border-left-color: transparent;
        }

        .zigzag-timeline .timeline-item::before {
            left: 15px;
        }

        .zigzag-timeline .timeline-icon {
            left: 0;
        }
    }
</style>
