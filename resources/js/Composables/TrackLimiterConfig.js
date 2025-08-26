export function removeTrackOptions(filterOption) {
    const options = [
        { value: 'default-end', text: 'From the end of default order' },
        { value: 'default-start', text: 'From the start of default order' },
        { value: 'oldest', text: 'Oldest tracks first' },
        { value: 'newest', text: 'Newest tracks first' },
        { value: 'random', text: 'Random tracks' },
    ];

    if (filterOption) {
        return options.filter((option) => {
            return option.value === filterOption;
        });
    }

    return options;
}
